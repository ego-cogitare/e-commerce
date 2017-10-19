<?php
    namespace Controllers;

    class AuthController
    {
        private $auth = null;
        
        public function __construct() {
            $this->auth = new \Services\AuthService();
        }
        
        public function __invoke($request, $response, $args) 
        {
            switch ($request->getUri()->getPath()) {
                case '/login':
                    $username = $request->getParam('username');
                    $password = $request->getParam('password');

                    if (empty($username) || empty($password)) {
                        return $response->withStatus(400)->write('Incorrect username and/or password.');
                    }

                    if ($this->auth->loginAttempt($username, $password)) {
                        return $response->withStatus(204);
                    }
                    else {
                        return $response->withStatus(400)->write('Incorrect username and/or password.');
                    }
                break;
                
                case '/logout':
                    if ($this->auth->logout()) {
                        return $response->withStatus(203);
                    }
                break;
            
                default:
                    return $response->withStatus(405);
                break;
            }
        }
    }