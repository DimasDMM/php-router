<?php
namespace Table;

use Utils\Dates;
use Core\Table\Table;
use Utils\Connection;

final class Users implements Table
{
    /** @var Connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $idUser
     * @return array|null
     */
    private function getRolesByIdUser($idUser) {
        $idUser = $this->connection->escapeSqlString($idUser);

        $sql =
            "SELECT " .
                "roles_groups.id AS `id_group`, " .
                "roles_groups.name AS `group`, " .
                "roles_data.id AS `role` " .
            "FROM " .
                "`" . TABLE_ROLES_GROUPS . "` AS roles_groups, " .
                "`" . TABLE_USERS_DATA . "` AS users_data, " .
                "`" . TABLE_ROLES_DATA . "` AS roles_data, " .
                "`" . TABLE_ROLES_REL . "` AS roles_rel " .
            "WHERE " .
                "users_data.id_user = " . $idUser . " AND " .
                "roles_groups.id = users_data.id_role_group AND " .
                "roles_groups.id = roles_rel.id_role_group AND " .
                "roles_data.id = roles_rel.id_role";
        $result = $this->connection->execSQL($sql);

        if ($this->connection->mysqli_errno == 0) {
            return null;
        }

        $roles = array();
        $idRoleGroup = null;
        $roleGroup = '';
        foreach ($result as $role) {
            $roles[] = $role['role'];
            $roleGroup = $role['group'];
            $idRoleGroup = $role['id_group'];
        }

        return array(
            'id_role_group' => $idRoleGroup,
            'role_group' => $roleGroup,
            'roles' => $roles
        );
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function updateLastOnline($id)
    {
        $id = $this->connection->escapeSqlString($id);

        $sql =
            "UPDATE `" . TABLE_USERS_LOGIN . "` " .
            "SET " .
                "`last_online` = '" . Dates::getDatetimeNow() . "', " .
                "`date_update` = '" . Dates::getDatetimeNow() . "' " .
            "WHERE id = " . $id;
        $this->connection->execSQL($sql);

        return $this->connection->mysqli_errno == 0;
    }

    /**
     * @param int $id
     * @param string $username
     * @return array|null
     */
    public function getUserByIdOrUsername($id = 0, $username = '') {
        if ($id > 0) {
            $where = "user_login.id = ". $id ." ";
        } else {
            $username = $this->connection->escapeSqlString($username);
            $where = "user_login.username = '". $username ."'";
        }

        $sql =
            "SELECT " .
                "user_login.id AS id, " .
                "user_login.username AS username, " .
                "user_login.password AS password, " .
                "user_login.is_active AS is_active, " .
                "user_data.name AS name, " .
                "user_data.surname AS surname, " .
                "user_data.alias AS alias, " .
                "user_data.email AS email, " .
                "user_login.date_insert AS date_insert, " .
                "user_data.date_update AS date_update_data " .
            "FROM " .
                "`" . TABLE_USERS_LOGIN . "` AS user_login, " .
                "`" . TABLE_USERS_DATA . "` AS user_data " .
            "WHERE " .
                $where . " AND " .
                "user_login.id = user_data.id_user";
        $result = $this->connection->execSql($sql);

        return empty($result[0]) ? null : $result[0];
    }
}
