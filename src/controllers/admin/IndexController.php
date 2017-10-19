<?php
    namespace Admin\Controllers;

    class IndexController
    {
        public function index($request, $response)
        {
            $param = $request->getParam('name');
            $params = $request->getParams();
            
            return $response->withStatus(200)->write(
                json_encode(\Admin\Models\User::fetchAll()->toArray())
            );
        }
    }