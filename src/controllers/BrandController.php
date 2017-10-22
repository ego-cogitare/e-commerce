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
        
        public function get($request, $response, $args) 
        {
            $brand = \Models\Brand::fetchOne([
                'id' => $args['id']
            ]);
            
            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Брэнд не найден'
                    ])
                );
            }
            
            $pictures = $brand->pictures ?? [];
            
            $pictures = array_map(function($id) {
                return \Models\Media::fetchOne([ 'id' => $id ])->toArray();
            }, $pictures);
            
            $brand->pictures = $pictures;
            
            return $response->write(
                json_encode($brand->toArray())
            );
        }
        
        public function add($request, $response) 
        {
            $params = $request->getParams();
            
            if (empty($params['title']) && empty($params['pictures'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
                    ])
                );
            }
            
            $brand = new \Models\Brand();
            $brand->title = $params['title'];
            $brand->save();
            
            return $response->write(
                json_encode($brand->toArray())
            );
        }
        
        public function update($request, $response) 
        {
            $params = $request->getParams();
            
            if (empty($params['title']) && empty($params['pictures'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
                    ])
                );
            }
            
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
                $brand = new \Models\Brand();
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
            
            $brand->pictures = array_map(function($id) {
                return \Models\Media::fetchOne([ 'id' => $id ])->toArray();
            }, $pictures);
            
            return $response->write(
                json_encode($brand->toArray())
            );
        }
    }