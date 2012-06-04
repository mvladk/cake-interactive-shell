<?php
$ds = DIRECTORY_SEPARATOR;
$dispatcher = 'Cake' . $ds . 'Console' . $ds . 'ShellDispatcher.php';

if (function_exists('ini_set')) {
	$root = dirname(dirname(dirname(__FILE__)));
	ini_set('include_path', $root . $ds. 'lib' . PATH_SEPARATOR . ini_get('include_path'));
}

if (!include($dispatcher)) {
	trigger_error('Could not locate CakePHP core files.', E_USER_ERROR);
}

unset($paths, $path, $dispatcher, $root);

$appFolder = dirname(__DIR__);
$loader = new ShellDispatcher(array('ci', '-working', $appFolder));
if (file_exists($appFolder . $ds . 'Config' . $ds . 'includes.php')) {
	include($appFolder . $ds . 'Config' . $ds . 'includes.php');
}

unset($appFolder, $loader, $ds);