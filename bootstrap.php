<?php    
    require_once 'vendor/autoload.php';
    
    /**
     * Include all models
     */
    $models = glob(__DIR__ . '/src/models/*.php');
    foreach ($models as $model) {
        require_once $model;   
    }
    
    /**
     * Include all controllers
     */
    $controllers = glob(__DIR__ . '/src/controllers/*.php');
    foreach ($controllers as $controller) {
        require_once $controller;   
    }
    
    $config = [
	'settings' => [
            'displayErrorDetails' => true,
            'determineRouteBeforeAppMiddleware' => false,
            'mysql' => [
                'driver' => 'mysql',
                'host' => '192.168.0.2',
                'port' => '3306',
                'database' => 'e_commerce',
                'username' => 'root',
                'password' => '12345',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => ''
            ],
            'mongo' => [
                'driver' => 'mongodb',
                'servers' => [
                    ['host' => '192.168.0.2', 'port' => '27017'],
                ],
                'db' => 'e_commerce',
            ],
	],
    ];
    
    MongoStar\Config::setConfig($config['settings']['mongo']);
    
    $app = new \Slim\App($config);
    
    require_once __DIR__ . '/src/routes.php';
    
    /**
     * Instantiate app
     */
    return $app;    
    

