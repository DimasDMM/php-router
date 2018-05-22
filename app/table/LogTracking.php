<?php
namespace Table;

use Utils\Dates;
use Core\Table\Table;
use Utils\Connection;

final class LogTracking implements Table
{
    /** @var Connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $idUser You can set it as 'empty string' if no user is logged
     * @param string $hashVisit
     * @param string $ip Optional
     * @param string $refererUrl Optional
     * @param string $refererHashPage Optional
     * @param string $refererHashFull Optional
     * @param string $browserRaw Optional
     * @param string $browserFix Optional
     * @param string $browserVersion Optional
     * @param string $operatingSystem Optional
     * @return bool
     */
    public function insert(
        $idUser,
        $hashVisit,
        $ip = '',
        $refererUrl = '',
        $refererHashPage = '',
        $refererHashFull = '',
        $browserRaw = '',
        $browserFix = '',
        $browserVersion = '',
        $operatingSystem = ''
    ) {
        if (empty($hashVisit)) {
            return false;
        }

        // Parsear los parametros
        $idUser = empty($idUser) ? 'NULL' : $idUser;
        $hashVisit = empty($hashVisit) ? 'NULL' : "'" . $this->connection->escapeSqlString($hashVisit) . "'";
        $ip = empty($ip) ? 'NULL' : "'" . $this->connection->escapeSqlString($ip) . "'";
        $refererUrl = empty($refererUrl) ? 'NULL' : "'" . $this->connection->escapeSqlString($refererUrl) . "'";
        $refererHashPage = empty($refererHashPage) ? 'NULL' : "'" . $this->connection->escapeSqlString($refererHashPage) . "'";
        $refererHashFull = empty($refererHashFull) ? 'NULL' : "'" . $this->connection->escapeSqlString($refererHashFull) . "'";
        $browserRaw = empty($browserRaw) ? 'NULL' : "'" . $this->connection->escapeSqlString($browserRaw) . "'";
        $browserFix = empty($browserFix) ? 'NULL' : "'" . $this->connection->escapeSqlString($browserFix) . "'";
        $browserVersion = empty($browserVersion) ? 'NULL' : "'" . $this->connection->escapeSqlString($browserVersion) . "'";
        $operatingSystem = empty($operatingSystem) ? 'NULL' : "'" . $this->connection->escapeSqlString($operatingSystem) . "'";

        // Comprobar si acabamos de insertar un registro
        $sql =
            "SELECT id " .
            "FROM `" . TABLE_LOG_TRACKING . "` " .
            "WHERE " .
                "hash_visit = " . $hashVisit . " AND " .
                "date_insert > DATE_SUB('" . Dates::getDatetimeNow() . "', INTERVAL 5 SECOND)";
        $result = $this->connection->execSQL($sql);

        if (!empty($result[0])) {
            return false;
        }

        // Insert if there isn't any previous record with the same 'hash_visit'
        $sql =
            "INSERT INTO `" . TABLE_LOG_TRACKING . "`( " .
                "id_user, " .
                "hash_visit, " .
                "ip, " .
                "referer_url, " .
                "referer_hash_page, " .
                "referer_hash_full, " ."browser_raw, " .
                "browser_fix, " .
                "browser_version, " .
                "operating_system, " .
                "date_insert " .
            ") VALUES ( " .
                $idUser . ", " .
                $hashVisit . ", " .
                $ip . ", " .
                $refererUrl . ", " .
                $refererHashPage . ", " .
                $refererHashFull . ", " .
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
     * Returns number of unique visitors (-1 if error)
     *
     * @param int $days Optional If set: "current_day - $days" to "current_day"
     * @return int
     */
    public function getNumberUniqueVisitors($days = 0)
    {
        $sqlWhere = "";
        if ($days > 1) {
            $sqlWhere = "WHERE date_insert > DATE_SUB('" . Dates::getDatetimeNow() . "', INTERVAL " . $days . " DAY)";
        }

        $sql =
            "SELECT " .
                "COUNT( DISTINCT ip ) AS total " .
            "FROM `" . TABLE_LOG_TRACKING . "` " .
            $sqlWhere;
        $result = $this->connection->execSQL($sql);

        if ($this->connection->mysqli_errno != 0) {
            return -1;
        }

        return isset($result[0]) ? $result[0]['total'] : 0;
    }

    /**
     * Returns number of logged visitors (-1 if error)
     *
     * @param int $days Optional If set: "current_day - $days" to "current_day"
     * @return int
     */
    public function getNumberMembersVisitors($days = 0)
    {
        $sqlWhere = "";
        if ($days > 1) {
            $sqlWhere = "AND date_insert > DATE_SUB('" . Dates::getDatetimeNow() . "', INTERVAL " . $days . " DAY)";
        }

        $sql =
            "SELECT " .
                "COUNT( DISTINCT ip ) AS total " .
            "FROM `" . TABLE_LOG_TRACKING . "` " .
            "WHERE " .
                "id_user IS NOT NULL AND " .
                "id_user != '' " .
                $sqlWhere;
        $result = $this->connection->execSQL($sql);

        if ($this->connection->mysqli_errno != 0) {
            return -1;
        }

        return isset($result[0]) ? $result[0]['total'] : 0;
    }

    /**
     * Number of pages viewed (including logged users) (-1 if error)
     *
     * @param int $days Optional If set: "current_day - $days" to "current_day"
     * @return int
     */
    public function getNumberViewsTotal($days = 0)
    {
        $sqlWhere = "";
        if ($days > 1) {
            $sqlWhere = "WHERE date_insert > DATE_SUB('" . Dates::getDatetimeNow() . "', INTERVAL " . $days . " DAY)";
        }

        $sql =
            "SELECT " .
                "COUNT(*) AS total " .
            "FROM `" . TABLE_LOG_TRACKING . "` " .
            $sqlWhere;
        $result = $this->connection->execSQL($sql);

        if ($this->connection->mysqli_errno != 0) {
            return -1;
        }

        return isset($result[0]) ? $result[0]['total'] : 0;
    }

    /**
     * Number of viewed pages (only logged users) (-1 if error)
     *
     * @param int $days Optional If set: "current_day - $days" to "current_day"
     * @return int
     */
    public function getNumberViewsMembers ($days = 0)
    {
        $sqlWhere = "";
        if ($days > 1) {
            $sqlWhere = "AND date_insert > DATE_SUB('" . Dates::getDatetimeNow() . "', INTERVAL " . $days . " DAY)";
        }

        $sql =
            "SELECT " .
                "COUNT(*) AS total " .
            "FROM `" . TABLE_LOG_TRACKING . "` " .
            "WHERE " .
                "id_user IS NOT NULL AND " .
                "id_user != '' " .
                $sqlWhere;
        $result = $this->connection->execSQL($sql);

        if ($this->connection->mysqli_errno != 0) {
            return -1;
        }

        return isset($result[0]) ? $result[0]['total'] : 0;
    }

    /**
     * Get percetage of viewed pages by logged users and non-logged users (-1 if error)
     *
     * @return int
     */
    public function getPercentViewsMembers()
    {
        $totalViews = $this->getNumberViewsTotal();
        $totalViewsMembers = $this->getNumberViewsMembers();

        if ($totalViews == -1 || $totalViewsMembers == -1) {
            return -1;
        }

        $percent = $totalViewsMembers / $totalViews * 100;
        return number_format($percent, 4);
    }

    /**
     * Get the number of viewed pages and unique visitors, sorted by day
     *
     * @param int $days Optional If set: "current_day - $days" to "current_day"
     * @return array
     */
    public function getNumberVisitsFrom($days = 30)
    {
        $result = array();

        $sql =
            "SELECT " .
                "COUNT(DISTINCT ip) AS `unique_visitors`, " .
                "COUNT(*) AS `total_views`, " .
                "DATE_FORMAT(date_insert, '%Y-%m-%d') AS `date` " .
            "FROM " .
                "`" . TABLE_LOG_TRACKING . "` " .
            "WHERE " .
                "date_insert > DATE_SUB('" . Dates::getDatetimeNow() . "', INTERVAL " . $days . " DAY) " .
            "GROUP BY " .
                "CAST(date_insert AS DATE) " .
            "ORDER BY date_insert ASC";
        $resultTemp = $this->connection->execSQL($sql);

        // If there are empty days, fill them
        $dateStart = date('Y-m-d', strtotime( Dates::getDateNow() . ' -31 days'));
        $dateEnd = Dates::getDateNow();
        $dateLoop = $dateStart;
        $i = 0;

        while ($dateLoop != $dateEnd) {
            $dateLoop = date('Y-m-d', strtotime($dateLoop . ' +1 day'));

            if (empty($resultTemp[$i]['date']) || $resultTemp[$i]['date'] != $dateLoop) {
                $result[] = array(
                    'unique_visitors' => 0,
                    'total_views' => 0,
                    'date' => $dateLoop
                );
            } else {
                $result[] = array(
                    'unique_visitors' => $resultTemp[$i]['unique_visitors']+0,
                    'total_views' => $resultTemp[$i]['total_views']+0,
                    'date' => $dateLoop
                );
                $i++;
            }
        }

        return $result;
    }

    /**
     * Get the number of viewed pages by operating system
     *
     * @param int $days Optional If set: "current_day - $days" to "current_day"
     * @return array
     */
    public function getNumberOperatingSystem($days = 0)
    {
        $sqlWhere = "";
        if ($days > 1) {
            $sqlWhere = "WHERE date_insert > DATE_SUB('" . Dates::getDatetimeNow() . "', INTERVAL " . $days . " DAY)";
        }

        $sql =
            "SELECT " .
                "SUM(operating_system LIKE '%Windows%') AS windows, " .
                "SUM(operating_system LIKE '%Linux%') AS linux, " .
                "SUM(operating_system LIKE '%Mac%') AS mac, " .
                "SUM(operating_system LIKE '%Android%') AS android, " .
                "SUM(operating_system LIKE '%iOS%') AS ios, " .
                "SUM(operating_system IS NULL OR operating_system = '') AS unknown " .
            "FROM " .
                "`" . TABLE_LOG_TRACKING . "` " .
            $sqlWhere;
        return $this->connection->execSQL($sql);
    }

    /**
     * Get the number of viewed pages by browser
     *
     * @param int $days Optional If set: "current_day - $days" to "current_day"
     * @return array
     */
    public function getNumberBrowser($days=0)
    {
        $sqlWhere = "";
        if ($days > 1) {
            $sqlWhere = "WHERE date_insert > DATE_SUB('" . Dates::getDatetimeNow() . "', INTERVAL " . $days . " DAY)";
        }

        $sql =
            "SELECT " .
                "SUM(browser_fix LIKE '%Chrome%') AS chrome, " .
                "SUM(browser_fix LIKE '%Firefox%') AS firefox, " .
                "SUM(browser_fix LIKE '%Edge%') AS edge, " .
                "SUM(browser_fix LIKE '%Opera%') AS opera, " .
                "SUM(browser_fix LIKE '%Safari%') AS safari, " .
                "SUM(browser_fix LIKE '%Explorer%') AS ie, " .
                "SUM(browser_fix IS NULL OR browser_fix = '') AS unknown " .
            "FROM " .
                "`" . TABLE_LOG_TRACKING . "` " .
            $sqlWhere;
        return $this->connection->execSQL($sql);
    }
}
