<?php
namespace Table;

use Utils\Dates;
use Core\Table\Table;
use Utils\Connection;

final class Session implements Table
{
	/** @var Connection */
	protected $connection;

    public function __construct(Connection $connection)
    {
		$this->connection = $connection;
	}

	/**
	 * @param int $user
	 * @param string $hash
	 * @return bool
	 */
	public function insert($user, $hash)
    {
		$user = $this->connection->escapeSqlString($user);
		$hash = $this->connection->escapeSqlString($hash);

		// Insertar
		$sql =
			"INSERT INTO `". TABLE_SESS ."`( " .
				"`id_user`, `cookie`, date_update, date_insert " .
			") VALUES ( " .
				$user . ", " .
				"'" . $hash . "', " .
				"'" . Dates::getDatetimeNow() . "', " .
				"'" . Dates::getDatetimeNow() . "' " .
			")";
		$this->connection->execSQL($sql);

		return ($this->connection->mysqli_errno == 0);
	}

	/**
	 * @param string $hash
	 * @return array|null
	 */
	public function get($hash)
    {
		$hash = $this->connection->escapeSqlString($hash);

		$sql =
			"SELECT * " .
			"FROM `" . TABLE_SESS . "` " .
			"WHERE `cookie` = '" . $hash . "' ";
		$result = $this->connection->execSQL($sql);

		return empty($result[0]) ? null : $result[0];
	}

	/**
	 * @param string $hash
	 * @return boolean
	 */
	public function update($hash)
    {
		$hash = $this->connection->escapeSqlString($hash);

		$sql =
			"UPDATE `" . TABLE_SESS . "` " .
			"SET " .
				"date_update = '" . Dates::getDatetimeNow() . "' " .
			"WHERE " .
				"`cookie` = '" . $hash . "' ";
		$this->connection->execSQL($sql);

		return $this->connection->mysqli_errno == 0;
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function delete($hash)
    {
		$hash = $this->connection->escapeSqlString($hash);

		$sql = "DELETE FROM `" . TABLE_SESS . "` WHERE `cookie` = '" . $hash . "' ";
		$this->connection->execSQL($sql);

		return $this->connection->mysqli_errno == 0;
	}
}
