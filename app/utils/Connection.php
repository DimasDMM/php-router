<?php
/**
 * Class Connection
 *
 * It has all the necessary methods to establish a connection with a database.
 *
 * NOTICE: This class allows an only connection. To do more connections, this class
 * has to be modified
 */

namespace Utils;

class Connection
{
    protected $connection;
    public $mysqli_error = '';
    public $mysqli_errno = 0;

    private $mysqli_timeout = 10; // Timeout to connect

    public function __destruct()
    {
        if (!empty($this->connection)) {
            $this->closeMySQL();
        }
    }

    /**
     * @return null|\mysqli
     */
    public function getConnection()
    {
        return empty($this->connection) ? null : $this->connection;
    }

    /**
     * Create a connection with the database. To connect to a specific por, the $ip
     * value should have the following format: "IP:Port".
     * It returns false in case of error
     *
     * @param string $ip Examples: "localhost", "http://192.168.0.3", "http://127.0.0.1:3306"
     * @param string $user
     * @param string $pass
     * @param string $db
     * @return boolean|\mysqli
     */
    public function connectMySQL($ip, $db, $user, $pass)
    {
        if (empty($ip) || empty($db)) {
            return false;
        }

        if (preg_match('/^(.+):(\d+)$/', $ip, $m)) {
            $ip = $m[1];
            $port = $m[2];
        } else {
            $port = ini_get("mysqli.default_port");
        }

        $connection = mysqli_init();
        mysqli_options($connection, MYSQLI_OPT_CONNECT_TIMEOUT, $this->mysqli_timeout);

        @mysqli_real_connect($connection, $ip, $user, $pass, $db, $port);

        if (mysqli_connect_error() != '') {
            $this->mysqli_error = mysqli_connect_error();
            $this->mysqli_errno = mysqli_connect_errno();
            $connection = false;
        }

        $this->connection = $connection;
        $this->execSQL("SET NAMES 'utf8'");
        return $connection;
    }

    /**
     * @return boolean
     */
    public function closeMySQL()
    {
        $connection = $this->getConnection();
        if (empty($connection)) {
            return false;
        }

        @mysqli_close($connection);
        $this->connection = false;
        return true;
    }

    /**
     * Executes a query and returns the result as array
     *
     * @param string $sql
     * @return array|false
     */
    public function execSQL($sql)
    {
        $connection = $this->getConnection();
        if (empty($connection)) {
            return false;
        }

        // Clear previous results
        while (@mysqli_next_result($connection));

        $result = mysqli_query($connection, $sql);

        $this->mysqli_errno = mysqli_errno($connection);
        $this->mysqli_error = mysqli_error($connection);

        $rows = array();
        while ($row = @mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        @mysqli_free_result($result);

        return $rows;
    }

    /**
     * @return int|string
     */
    public function lastInsertedId()
    {
        return mysqli_insert_id($this->getConnection());
    }

    /**
     * Check that the table exists
     *
     * @param string $table
     * @return bool
     */
    public function existsTable($table)
    {
        $sql = 'DESCRIBE `' . $table . '`';
        $result = $this->execSQL($sql);
        return !empty($result);
    }

    /**
     * @param string $string
     * @return string
     */
    public function escapeSqlString($string)
    {
        return mysqli_real_escape_string($this->getConnection(), $string);
    }
}
