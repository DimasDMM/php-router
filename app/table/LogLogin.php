<?php
namespace Table;

use Utils\Dates;
use Core\Table\Table;
use Utils\Connection;

final class LogLogin implements Table
{
	/** @var Connection */
	protected $connection;

    public function __construct(Connection $connection)
    {
		$this->connection = $connection;
	}

	/**
	 * @param int $idUser
	 * @param int $msgType Optional 0: OK, 1: Error
	 * @param string $msgStr Optional
	 * @param string $ip Optional
	 * @param string $refererUrl Optional
	 * @param string $browserRaw Optional
	 * @param string $browserFix Optional
	 * @param string $browserVersion Optional
	 * @param string $operatingSystem Optional
	 * @return bool
	 */
	public function insert(
        $idUser,
        $msgType = 0,
        $msgKey = '',
        $ip = '',
        $refererUrl = '',
        $browserRaw = '',
        $browserFix = '',
        $browserVersion = '',
        $operatingSystem = ''
    ) {
		if (empty($idUser)) {
			return false;
        }

		// Parse params
		$idUser = empty($idUser) ? 'NULL' : $idUser;
		$msgType = empty($msgType) ? '0' : $msgType;
		$msgKey = empty($msgKey) ? 'NULL' : "'" . $this->connection->escapeSqlString($msgKey) . "'";
		$ip = empty($ip) ? 'NULL' : "'" . $this->connection->escapeSqlString($ip) . "'";
		$refererUrl = empty($refererUrl) ? 'NULL' : "'" . $this->connection->escapeSqlString($refererUrl) . "'";
		$browserRaw = empty($browserRaw) ? 'NULL' : "'" . $this->connection->escapeSqlString($browserRaw) . "'";
		$browserFix = empty($browserFix) ? 'NULL' : "'" . $this->connection->escapeSqlString($browserFix) . "'";
		$browserVersion = empty($browserVersion) ? 'NULL' : "'" . $this->connection->escapeSqlString($browserVersion) . "'";
		$operatingSystem = empty($operatingSystem) ? 'NULL' : "'" . $this->connection->escapeSqlString($operatingSystem) . "'";

		// Insert
		$sql =
			"INSERT INTO `" . TABLE_LOG_LOGIN . "`( " .
				"id_user, msg_type, msg_key, ip, referer_url, browser_raw, browser_fix, browser_version, operating_system, date_insert " .
			") VALUES ( " .
				$idUser . ", " .
				$msgType . ", " .
				$msgKey . ", " .
				$ip . ", " .
				$refererUrl . ", " .
				$browserRaw . ", " .
				$browserFix . ", " .
				$browserVersion . ", " .
				$operatingSystem . ", " .
				"'" . Dates::getDatetimeNow() . "' " .
			")";
		$this->connection->execSQL($sql);

		return $this->connection->mysqli_errno == 0;
	}

	/**
	 * @param int $from Optional
	 * @param int $to Optional
	 * @param int $idUser Optional
	 * @return array|null
	 */
	public function getFrom($from = 0, $to = 20, $idUser = null)
    {
		$sqlWhere = '';
		if (!empty($idUser)) {
			$sqlWhere = "WHERE id_user = " . $idUser . " ";
        }

		$count = $to - $from;
		if ($count <= 0) {
            return null;
        }

		$sql =
			"SELECT " .
				"* " .
			"FROM " .
				"`" . TABLE_LOG_LOGIN . "` " .
			$sqlWhere .
			"ORDER BY date_insert DESC " .
			"LIMIT " . $from . ", " . $count . " ";
		$result = $this->connection->execSQL($sql);

		return empty($result[0]) ? null : $result;
	}

	/**
	 * @param int $msgKey
	 * @param string $msgType
	 * @return string
	 */
	public static function parseMsg($msgType, $msgKey)
    {
		if ($msgType == 0) {
			return 'Ok';
        }

		switch($msgKey) {
			case 'pass_match':
				$strMsg = 'Wrong password';
				break;
			case 'user_disabled':
				$strMsg = 'Disabled user';
				break;
			default:
				$strMsg = 'Unknown error';
		}

		return $strMsg;
	}
}
