<?php
    namespace Controllers;

    class BrandController
    {
        public function index($request, $response)
        {
            $limit = $request->getParam('limit');
            $offset = $request->getParam('offset');
            
            return $response->withStatus(200)->write(
                json_encode(\Models\Brand::fetchAll()->toArray())
            );
        }
        
        public function add($request, $response) 
        {
            $params = $request->getParams();
            
//            if (empty($params['title'])) {
//                return $response->withStatus(400)->write(
//                    json_encode([
//                        'error' => 'Пустое название брэнда недопустимо'
//                    ])
//                );
//            }
            
            $brand = new \Models\Brand();
            $brand->title = $params['title'];
            $brand->save();
            
            return $response->write(
                json_encode($brand->toArray())
            );
        }
        
        public function addPicture($request, $response) 
        {
            $params = $request->getParams();
            
            $brand = \Models\Brand::fetchOne([
                'id' => $params['brandId']
            ]);
            
            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Брэнд не найден'
                    ])
                );
            }
            
            if (empty($params['pictureId'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Изображение брэнда не задано'
                    ])
                );
            }
            
            $pictures = $brand->pictures ?? [];
            $pictures[] = $params['pictureId'];
            $brand->pictures = $pictures;
            $brand->save();
            
            return $response->write(
                json_encode($brand->toArray())
            );
        }
    }