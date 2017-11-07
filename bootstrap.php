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
            'files' => [
                'upload' => [
                    // Keep original file names
                    'keepNames' => false,

                    // Destination directory to upload files
                    'directory' => __DIR__ . '/public'
                ]
            ]
       ],
    ];

    MongoStar\Config::setConfig($config['settings']['mongo']);

    $app = new \Slim\App($config);

    // Remove trailing slashes to all routes
    $app->add(new TrailingSlash(false));

    require_once __DIR__ . '/src/routes.php';

    $app->add(new TokenAuthentication([
        'path' => '',
        'passthrough' => ['/login', '/file'],
        'secure' => false,
        'regex' => '/^(.*)$/',
        'parameter' => 'token',
        'authenticator' => function($request, TokenAuthentication $tokenAuth) {
            // $tokenAuth->findToken($request);
            return (new \Services\AuthService)->isLoggedIn();
        }
    ]));

    $app->add(function($request, $response, $next) {
        $response = $next($request, $response);
        return $response->withHeader('Content-Type', 'application/json');
    });

    /**
     * Instantiate app
     */
    return $app;
