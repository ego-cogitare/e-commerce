<?php
    namespace Services;
    
    class AuthService
    {
        private $user = null;
        
        public function __construct() {
            $this->user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
        }
        
        public function loginAttempt($username, $password) {
            $user = \Models\User::fetchOne([
                'username' => $username,
            ]);
            
            if (!$user) {
                return false;
            }
            
            if (password_verify($password, $user->password)) {
                $this->login($user->toArray());
                
                return $user->toArray();
            }
            
            return false;
        }
        
        public function isLoggedIn() {
            return !is_null($this->user);
        }
        
        private function login(array $user) {
            $_SESSION['user'] = $user;
        }
        
        public function logout() {
            unset($_SESSION['user']);
            
            return !isset($_SESSION['user']);
        }
    }
