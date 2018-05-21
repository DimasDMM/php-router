<?php
/* Web variables */
ini_set('safe_mode','0');
date_default_timezone_set('Europe/Madrid');
header('Content-Type: text/html; charset=UTF-8');
header('Content-language: es');
session_start();

/* Local testing */
define('LOCALHOST', true);
define('LOCAL_SUBDIR', 'mvc_template');
define('TITLE', 'MVC Template');
define('HOST', 'host.local');
define('URL', 'http://' . HOST . '/');

/* MySQL */
define('MYSQL_SERVER', '127.0.0.1');
define('MYSQL_DATABASE', 'mvc_template');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');

define('TABLE_LOG_TRACKING', 'log_tracking');
define('TABLE_LOG_LOGIN', 'log_login');
define('TABLE_USERS_LOGIN', 'users_login');
define('TABLE_USERS_DATA', 'users_data');

define('TABLE_ROLES_GROUPS', 'roles_groups');
define('TABLE_ROLES_DATA', 'roles_data');
define('TABLE_ROLES_REL', 'roles_rel');

define('TABLE_SESSION', 'session');

/* Rutas */
define('PATH_TEMP', 'tmp/');
define('PATH_APP', 'app/');
define('PATH_CONF', PATH_APP . 'conf/');
define('PATH_CORE', PATH_APP . 'core/');
define('PATH_TABLES', PATH_APP . 'table/');
define('PATH_UTILS', PATH_APP . 'utils/');
define('PATH_CONTROLLER', PATH_APP . 'controller/');
define('PATH_VIEW', PATH_APP . 'view/');

/* Seguridad */
define('MIN_PASSWORD_LENGTH', 6);
define('MAX_PASSWORD_LENGTH', 40);
define('SALT_ACCOUNT', ''); // IMPORTANT: FILL THIS VALUE WITH SOMETHING RANDOM
