<?php

define('ROOTDIR', realpath(dirname(__FILE__) . '/../'));
define('SQL_PROFILER', false);

$config = new \Phalcon\Config\Adapter\Ini(ROOTDIR . '/app/config/config.ini');

$loader = new \Phalcon\Loader();
$loader->registerDirs(
	array(
		ROOTDIR . '/app/controllers/',
		ROOTDIR . '/app/models/',
		ROOTDIR . '/app/vendor/'
	)
)->register();

require_once(ROOTDIR . '/app/config/di.php');
require_once(ROOTDIR . '/app/config/router.php');

$application = new \Phalcon\Mvc\Application($di);

try {
	echo $application->handle()->getContent();
} catch (Exception $e) {
	if ($application->config->app->debug == 0) {
		$application->response->redirect("error")->sendHeaders();
	} else {
		$s = get_class($e) . ": " . $e->getMessage() . "<br>" . " File=" . $e->getFile() . "<br>" . " Line="
		. $e->getLine() . "<br>" . $e->getTraceAsString();
	
		echo $s;
	}
}
