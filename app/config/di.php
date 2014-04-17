<?php

$di = new Phalcon\DI\FactoryDefault();

$di->setShared(
	'config',
	function () use ($config) {
		return $config;
	}
);

$di->setShared('response', 'Phalcon\Http\Response');
$di->setShared('request', 'Phalcon\Http\Request');

$di->setShared(
	'session',
	function () use ($config) {
		session_save_path(ROOTDIR . '/tmp/sessions/');
		$session = new Phalcon\Session\Adapter\Files();
		return $session;
	}
);

$di->setShared(
	'view',
	function () use ($config, $di) {
		$view = new Phalcon\Mvc\View();
		$view->setViewsDir(ROOTDIR . '/app/view/');
		$view->registerEngines(
			array(
				'.phtml' => function ($view, $di) use ($config) {
					$volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);
					$volt->setOptions(array(
						'compiledPath'      => ROOTDIR . '/tmp/volt/',
						'compiledExtension' => '.php',
						'compiledSeparator' => '_',
						'compileAlways'     => true
					));
					$compiler = $volt->getCompiler();
					$compiler->addFunction('rand', 'rand');
					
					return $volt;
				},
				".php" => 'Phalcon\Mvc\View\Engine\Php'
			)
		);
		return $view;
	}
);

$di->setShared(
	'tag', 
	function() {
		return new Phalcon\Tag();
	}
);

$di->setShared(
	'dispatcher', 
	function() {
		return new Phalcon\Mvc\Dispatcher();
	}
);

$di->setShared(
	'url', 
	function() use ($config) {
		$url = new \Phalcon\Mvc\Url();
		$https = ($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$uri = $_SERVER['HTTP_HOST'];
		$url->setBaseUri($https . $uri . '/');
		return $url;
	}
);

$di->setShared(
	'cache', 
	function() use ($app, $config) {
		$frontCache = new \Phalcon\Cache\Frontend\Output(array(
			"lifetime" => $app->config->app->cache_lifetime
		));
		if ($config->app->cache_apc) {
			$cache = new \Phalcon\Cache\Backend\Apc(
				$frontCache,
				array(
					'prefix' => $app->config->app->prefix
				)
			);
		} else {
			$cache = new \Phalcon\Cache\Backend\File(
				$frontCache,
				array(
					'prefix' => $app->config->app->prefix,
					'cacheDir' => ROOTDIR ."/tmp/cache/"
				)
			);
		}
		return $cache;
	}
);

$di->setShared(
	'db',
	function () use ($config, $di) {
		$connection = new \Phalcon\Db\Adapter\Pdo\Mysql($config->database->toArray());
		$connection->execute('SET NAMES UTF8', array());
		
		if(SQL_PROFILER) {
			$eventsManager = new \Phalcon\Events\Manager();
			$connection->setEventsManager($eventsManager);
			
			$profiler = $di->getProfiler();
			$logger = $di->getLogger('db');
			$eventsManager->attach('db', function($event, $connection) use ($profiler, $logger) {
				if($event->getType() == 'beforeQuery') {
					$profiler->startProfile($connection->getSQLStatement());
				}
				if($event->getType() == 'afterQuery') {
					$profiler->stopProfile();
					
					$profiles = $profiler->getProfiles();
					foreach($profiles as $profile) {
						$toLog = array(
							'statement' 	=> $profile->getSQLStatement(),
							'start_time' 	=> $profile->getInitialTime(),
							'final_time' 	=> $profile->getFinalTime(),
							'total_time' 	=> $profile->getTotalElapsedSeconds()
						);
						$logger->log( json_encode($toLog) );
					}
				}
			});
		}
		
		return $connection;
	}
);

$di->setShared(
	'modelsMetadata',
	function () use ($config) {
		if ($config->app->cache_apc !== 0) {
			$metaData = new Phalcon\Mvc\Model\MetaData\Apc(array(
				"lifetime" => 36000,
				"prefix"   => $config->app->prefix . "-meta-db-main"
			));
		} else {
			$metaData = new \Phalcon\Mvc\Model\Metadata\Files(array(
				'metaDataDir' => ROOTDIR . '/tmp/cache/'
			));
		}
		return $metaData;
	}
);

$di->set(
	'modelsCache', 
	function() use ($app, $config) {
		$frontCache = new \Phalcon\Cache\Frontend\Output(array(
			"lifetime" => $app->config->app->cache_lifetime
		));
		if ($config->app->cache_apc) {
			$cache = new \Phalcon\Cache\Backend\Apc(
				$frontCache,
				array(
					'prefix' => $app->config->app->prefix
				)
			);
		} else {
			$cache = new \Phalcon\Cache\Backend\File(
				$frontCache,
				array(
					'prefix' => $app->config->app->prefix,
					'cacheDir' => ROOTDIR ."/tmp/cache/"
				)
			);
		}
		return $cache;
	}
);

$di->setShared(
	'profiler', 
	function(){
		return new \Phalcon\Db\Profiler();
	}
);

$di->setShared(
	'transactions', 
	function(){
		return new \Phalcon\Mvc\Model\Transaction\Manager();
	}
);

$di->setShared(
	'logger', 
	function($name = 'db'){
		$logger = new \Phalcon\Logger\Adapter\File(ROOTDIR . '/tmp/logs/'. $name .'.txt');
		$logger->setFormatter(new \Phalcon\Logger\Formatter\Line("%date%\t%message%"));
		return $logger;
	}
);