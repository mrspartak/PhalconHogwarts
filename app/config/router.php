<?php
$router = new Phalcon\Mvc\Router();	

$router->handle();

$di->setShared(
	'router', 
	function() use ($router){
		return $router;
	}
);