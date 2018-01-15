<?php
    namespace Controllers\Backend;

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
                    if ($this->auth->isLoggedIn()) {
                        return $response->withStatus(400)->write(
                            json_encode(['error' => 'Already authorized.'])
                        );
                    }
                    
                    $username = $request->getParam('username');
                    $password = $request->getParam('password');

                    if (empty($username) || empty($password)) {
                        return $response->withStatus(400)->write(
                            json_encode(['error' => 'Incorrect username and/or password.'])
                        );
                    }

                    if ($user = $this->auth->loginAttempt($username, $password)) {
                        return $response->withStatus(200)->write(
                            json_encode($user)
                        );
                    }
                    else {
                        return $response->withStatus(400)->write(
                            json_encode(['error' => 'Incorrect username and/or password.'])
                        );
                    }
                break;
                
                case '/logout':
                    if ($this->auth->logout()) {
                        return $response->withStatus(204);
                    }
                break;
            
                default:
                    return $response->withStatus(404);
                break;
            }
        }
    }