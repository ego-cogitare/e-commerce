<?php
    use Psr7Middlewares\Middleware\TrailingSlash;
    use Slim\Middleware\TokenAuthentication;

    require_once 'vendor/autoload.php';

    session_start();

    /**
     * Include all models, controllers, etc...
     */
    $includes = [
        __DIR__ . '/src/interfaces/*.php',
        __DIR__ . '/src/models/*.php',
        __DIR__ . '/src/controllers/backend/*.php',
        __DIR__ . '/src/controllers/store/*.php',
        __DIR__ . '/src/services/*.php',
        __DIR__ . '/src/components/*.php',
    ];
    foreach ($includes as $pattern) {
        foreach (glob($pattern) as $script) {
            require_once $script;
        }
    }

    $config = [
        'settings' => [
            'appName' => 'Junimed',
            'siteUrl' => 'http://shop.junimed.ua',
            'displayErrorDetails' => true,
            'determineRouteBeforeAppMiddleware' => false,
            'mongo' => [
                'driver' => 'mongodb',
                'servers' => [
                    [
                        'host' => '127.0.0.1',
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
            ],
            /**
             * @link https://www.liqpay.ua/documentation/api/aquiring/checkout/doc
             */
            'liqpay' => [
                'publicKey' => 'i95189456725',
                'privateKey' => 'fCFrZ1E988gIR4BKItA0qIEHJzbDRGhPaPjBhfos',
                'server_url' => 'http://api.shop.junimed.ua/store/payment',
                'result_url' => 'http://shop.junimed.ua/checkout/thanks',
                'sandbox' => '1'
            ]
       ],
    ];

    MongoStar\Config::setConfig($config['settings']['mongo']);

    $app = new \Slim\App($config);

    // Remove trailing slashes to all routes
    $app->add(new TrailingSlash(false));

    require_once __DIR__ . '/src/routes/backend.php';
    require_once __DIR__ . '/src/routes/store.php';

    $app->add(new TokenAuthentication([
        'path' => '',
        'passthrough' => ['/login', '/file', '/store'],
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
