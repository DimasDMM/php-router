<?php
/**
 * List of public URLs
 *
 * Format:
 * $pages['page_xxx'] = array(
 *   'tracking' => 'identifier', // Usually are 6 random characters (min 4, max 32)
 *   'url' => 'my_url',
 *   'controller' => 'dir_in_controller_folder/File.php'
 * );
 *
 * Example:
 * $pages['page_home'] = array(
 *   'tracking' => 'a7xiu0',
 *   'url' => 'home',
 *   'controller' => 'frontend/Home.php'
 * );
 *
 * Public URL:
 * http://url.local/inicio
 *
 *
 * The script tries to match the most specific URLs. That is, if we have:
 * http://url.local/dir1/dir2
 * Then, the script tries to find any coincidence with "/dir/dir2". If no match
 * found, it continues with "/dir1". If not, it loads "/" (usually the home page)
 *
 * If match found but controller empty, it continues searching coincidences
 */

global $pagesList;
$pagesList = array();

// Home controller
$pagesList['index'] = array(
    'tracking' => 'a7xiu0',
    'url' => '',
    'controller' => 'frontend/Home.php'
);

// Sitemap
$pagesList['sitemap'] = array(
    'url' => 'sitemap.xml'
);

// Default pages - Please, don't modify them
$pagesList['page_error'] = array(
    'tracking' => '8ia9fs',
    'url' => 'error',
    'controller' => 'default/Error.php'
);
$pagesList['pixel_tracking'] = array(
    'tracking' => '5au12d',
    'url' => 'tracker',
    'controller' => 'default/Tracking.php'
);

// Public pages
$pagesList['page_home'] = array(
    'tracking' => '3fan78',
    'url' => 'home',
    'controller' => 'frontend/Home.php'
);
$pagesList['page_login'] = array(
    'tracking' => 'asko28',
    'url' => 'login',
    'controller' => 'frontend/Login.php'
);
