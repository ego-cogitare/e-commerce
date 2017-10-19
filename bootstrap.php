<?php
    use Psr7Middlewares\Middleware\TrailingSlash;
    use Slim\Middleware\TokenAuthentication;
    
    require_once 'vendor/autoload.php';
    
    session_start();
    
    /**
     * Include all models, controllers, etc...
     */
    $includes = [
        __DIR__ . '/src/models/*.php',
        __DIR__ . '/src/controllers/*.php',
        __DIR__ . '/src/services/*.php',
    ];
    foreach ($includes as $pattern) {
        foreach (glob($pattern) as $script) {
            require_once $script;
        }
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
                    [
                        'host' => 'mongodb.loc', 
                        'port' => '27017'
                    ],
                ],
                'db' => 'e_commerce',
            ],
	],
    ];

    MongoStar\Config::setConfig($config['settings']['mongo']);

    $app = new \Slim\App($config);
    
    // Remove trailing slashes to all routes
    $app->add(new TrailingSlash(false));
    
    require_once __DIR__ . '/src/routes.php';
    
    $app->add(new TokenAuthentication([
        'path' => '',
        'passthrough' => ['/login', '/logout'],
        'secure' => false,
        'regex' => '/^(.*)$/',
        'parameter' => 'token',
        'authenticator' => function($request, TokenAuthentication $tokenAuth) {
            return (new \Services\AuthService)->isLoggedIn();
        }
    ]));

    /**
     * Instantiate app
     */
    return $app;
