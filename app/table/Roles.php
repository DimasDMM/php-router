<?php
namespace Table;

use Utils\Dates;
use Core\Table\Table;
use Utils\Connection;

class Roles implements Table
{
	/** @var Connection */
	protected $connection;

    public function __construct(Connection $connection)
    {
		$this->connection = $connection;
	}

	/**
	 * @param string $id
	 * @return null|array
	 */
	public function getRole($id)
    {
		$id = $this->connection->escapeSqlString($id);

		$sql = "SELECT * FROM `" . TABLE_ROLES_DATA . "` WHERE id = '" . $id . "'";
		$result = $this->connection->execSQL($sql);

		if ($this->connection->mysqli_errno != 0) {
            return null;
        }

		return $result;
	}

	/**
	 * @param string $id
	 * @param string $name
	 * @return boolean
	 */
	public function insertRole($id, $name)
    {
		$id = $this->connection->escapeSqlString($id);
		$name = $this->connection->escapeSqlString($name);

		$sql =
			"INSERT INTO `" . TABLE_ROLES_DATA . "`( " .
				"id, `name`, `date_update`, date_insert " .
			") VALUES ( " .
				"'" . $id . "', " .
				"'" . $name . "', " .
				"'" . Dates::getDatetimeNow() . "', " .
				"'" . Dates::getDatetimeNow() . "' " .
			")";
		$this->connection->execSQL($sql);

		return $this->connection->mysqli_errno == 0;
	}

	/**
	 * @param string $idOld
	 * @param string $idNew
	 * @param string $name
	 * @return boolean
	 */
	public function updateRole($idOld, $idNew, $name)
    {
		$idOld = $this->connection->escapeSqlString($idOld);
		$idNew = $this->connection->escapeSqlString($idNew);
		$name = $this->connection->escapeSqlString($name);

		$sql =
			"UPDATE " .
				"`" . TABLE_ROLES_DATA . "` " .
			"SET " .
				"id = '" . $idNew . "', " .
				"`name` = '" . $name . "', " .
				"`date_update` = '" . Dates::getDatetimeNow() . "' " .
			"WHERE " .
				"id = '" . $idOld . "'";
		$this->connection->execSQL($sql);

		return $this->connection->mysqli_errno == 0;
	}

	/**
	 * @param string $id
	 * @return boolean
	 */
	public function deleteRole($id)
    {
		$id = $this->connection->escapeSqlString($id);

		$sql = "DELETE FROM `" . TABLE_ROLES_DATA . "` WHERE id = '" . $id . "'";
		$this->connection->execSQL($sql);

		if ($this->connection->mysqli_errno != 0) {
			return false;
        }

		$sql = "DELETE FROM `" . TABLE_ROLES_REL . "` WHERE id_role = '" . $id . "'";
		$this->connection->execSQL($sql);

		return ($this->connection->mysqli_errno == 0);
	}

	/**
	 * @param string $name
	 * @return false|int
	 */
	public function insertGroup($name)
    {
		$name = $this->connection->escapeSqlString($name);

		$sql =
			"INSERT INTO `" . TABLE_ROLES_GROUPS . "`( " .
				"`name`, date_update, date_insert " .
			") VALUES ( " .
				"'" . $name . "', " .
				"'" . Dates::getDatetimeNow() . "', " .
				"'" . Dates::getDatetimeNow() . "' " .
			")";
		$this->connection->execSQL($sql);

		return $this->connection->mysqli_errno == 0
            ? $this->connection->lastInsertedId()
            : false;
	}

	/**
	 * @param string $id
	 * @param string $name
	 * @return boolean
	 */
	public function updateGroup($id, $name)
    {
		$id = $this->connection->escapeSqlString($id);
		$name = $this->connection->escapeSqlString($name);

		$sql =
			"UPDATE " .
				"`" . TABLE_ROLES_GROUPS . "` " .
			"SET " .
				"name = '" . $name . "', " .
				"`date_update` = '" . Dates::getDatetimeNow() . "' " .
			"WHERE " .
				"id = '" . $id . "'";
		$this->connection->execSQL($sql);

		return $this->connection->mysqli_errno == 0;
	}

	/**
	 * @param string $id
	 * @return boolean
	 */
	public function deleteGroup($id)
    {
		$id = $this->connection->escapeSqlString($id);

		$sql = "DELETE FROM `" . TABLE_ROLES_GROUPS . "` WHERE id = '" . $id . "'";
		$this->connection->execSQL($sql);

		if ($this->connection->mysqli_errno != 0) {
            return false;
        }

		$this->deleteAllGroupRoles($id);

		return $this->connection->mysqli_errno == 0;
	}

	/**
	 * @param string $id Identificador del grupo
	 * @return boolean
	 */
	public function deleteAllGroupRoles($id)
    {
		$id = $this->connection->escapeSqlString($id);

		$sql = "DELETE FROM `" . TABLE_ROLES_REL . "` WHERE id_role_group = '" . $id . "' ";
		$this->connection->execSQL($sql);

		return $this->connection->mysqli_errno == 0;
	}

	/**
	 * @param int $idGroup
	 * @param string $idRole
	 * @return boolean
	 */
	public function addGroupRole($idGroup, $idRole)
    {
		$idGroup = $this->connection->escapeSqlString($idGroup);
		$idRole = $this->connection->escapeSqlString($idRole);

		$sql =
			"INSERT INTO `" . TABLE_ROLES_REL . "`( " .
				"`id_role_group`, id_role, date_insert " .
			") VALUES ( " .
				"'" . $idGroup . "', " .
				"'" . $idRole . "', " .
				"'" . Dates::getDatetimeNow() . "' " .
			")";
		$this->connection->execSQL($sql);

		return $this->connection->mysqli_errno == 0;
	}

	/**
	 * @return null|array
	 */
	public function getAllRoles() {
		$sql = "SELECT * FROM `" . TABLE_ROLES_DATA . "` ORDER BY `name` DESC";
		$result = $this->connection->execSQL($sql);

		if ($this->connection->mysqli_errno != 0) {
            return null;
        }

		return $result;
	}

	/**
	 * @return array|null
	 */
	public function getAllGroups() {
		$sql =
			"SELECT " .
				"roles_groups.id AS group_id, " .
				"roles_groups.name AS group_name, " .
				"roles_data.id AS role_id, " .
				"roles_data.name AS role_name " .
			"FROM " .
				"`" . TABLE_ROLES_REL . "` AS roles_rel, " .
				"`" . TABLE_ROLES_GROUPS . "` AS roles_groups, " .
				"`" . TABLE_ROLES_DATA . "` AS roles_data " .
			"WHERE " .
				"roles_groups.id = roles_rel.id_role_group AND " .
				"roles_data.id = roles_rel.id_role " .
			"ORDER BY roles_groups.name, roles_data.name DESC";
		$resultTmp = $this->connection->execSQL($sql);

        if ($this->connection->mysqli_errno != 0) {
            return null;
        }

        // Fix the final array with the groups and roles
		$result = array();
		foreach ($resultTmp as $groupTmp) {
			// Comprobar si ya hemos añadido el grupo al resultado final
			$pos = -1;
            $i = 0;
			foreach ($result as $group) {
				if ($group['id'] == $groupTmp['group_id']) {
					$pos = $i;
					break;
				}
				$i++;
			}

			// Add result
			if ($pos != -1) {
				$result[$i]['roles'][] = array(
                    'id' => $groupTmp['role_id'],
                    'name' => $groupTmp['role_name']
                );
			} else {
				$result[] = array(
					'id' => $groupTmp['group_id'],
					'name' => $groupTmp['group_name'],
					'roles'=> array(
						array(
							'id' => $groupTmp['role_id'],
							'name' => $groupTmp['role_name']
						)
					)
				);
			}

		}

		return $result;
	}
}
