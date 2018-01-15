<?php
    namespace Controllers\Backend;

    class IndexController
    {
        public function index($request, $response)
        {
            $param = $request->getParam('name');
            $params = $request->getParams();
            
            return $response->withStatus(200)->write(
                json_encode(\Models\User::fetchAll()->toArray())
            );
        }
    }