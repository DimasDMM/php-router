<?php
/**
 * Class ControllerDB
 *
 * If it isn't mandatory a database connection, we can set $needConnection as false.
 * Otherwise, it redirects to the 500 error page
 */

namespace Core\Controller;

use Utils;
use Core\Manager;
use Utils\Connection;

class ControllerDB extends Controller
{
    protected $needConnection;

    public function __construct(Manager $manager, $needConnection = false)
    {
        parent::__construct($manager);

        $connection = new Connection();
        $connection->connectMySQL(MYSQL_SERVER, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASS);

        $this->needConnection = $needConnection;

        if (empty($connection->getConnection()) && $this->needConnection) {
            $manager->redirectHttpCode(500);
        }

        $manager->setConnection($connection);
    }
}
