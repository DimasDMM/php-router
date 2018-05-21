<?php
global $msgOk, $msgError;
$msgOk = array();
$msgError = array();

/**
 * Generic errors
 */
$msgError['connection'] = 'Impossible to establish a connection with the database. Please, try again later.';
$msgError['sql_error_insert'] = 'An error occurred while trying to insert data.';
$msgError['sql_error_insert'] = 'An error occurred while trying to update data.';
$msgError['sql_error_insert'] = 'An error occurred while trying to delete data.';

/*
 * Login messages
 */

// Error
$msgError['user_pass_empty'] = 'There are empty fields. Please, verify the inputs before continue.';
$msgError['user_pass_match'] = 'The user/password does not match with any of our records.';

$msgError['user_disabled'] = 'The user is disabled. Contact with the admin for further details.';
$msgError['not_session'] = 'You have to log in to access to this zone.';
$msgError['not_role_page'] = 'You have no permission to access to this zone.';

// Ok
$msgOk['logout'] = 'Log out correctly.';
