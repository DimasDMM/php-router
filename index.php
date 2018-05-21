<?php
require('conf.php');

autoload();
$manager = new Core\Manager();
$manager->loadPage($_SERVER['REQUEST_URI']);

function autoload()
{
	autoloadDir(PATH_CONF);
	autoloadDir(PATH_CORE);
	autoloadDir(PATH_TABLES);
	autoloadDir(PATH_UTILS);
}

function autoloadDir($dir, $subdirAutoload = true)
{
	foreach (scandir($dir) as $filename) {
		if ($filename == '.' || $filename == '..') {
            continue;
        }

		$path = $dir . $filename;

		if (is_file($path)) {
			require($path);
		} elseif (is_dir($path)) {
			if ($subdirAutoload && file_exists($path . '/autoload.php')) {
				require($path . '/autoload.php');
			} else {
				$path .= '/';
				autoloadDir($path);
			}
		}
	}
}
