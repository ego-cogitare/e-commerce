<?php
    class HomeController
    {
        public function index($request, $response)
        {
            $param = $request->getParam('name');
            $params = $request->getParams();
            
//            var_dump($param);
//            var_dump($params);
            
            return $response->withStatus(200)->write(
                json_encode(\Model\User::fetchAll()->toArray())
            );
        }
    }