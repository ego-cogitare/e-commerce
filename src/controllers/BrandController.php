<?php
    namespace Controllers;

    class BrandController
    {
        public function index($request, $response)
        {
            $limit = $request->getParam('limit');
            $offset = $request->getParam('offset');
            
            $brands = array_map(function($brand) {
                $brand['pictures'] = array_map(function($pictureId) {
                    return \Models\Media::fetchOne(['id' => $pictureId])->toArray();
                }, $brand['pictures']);
                return $brand;
            }, \Models\Brand::fetchAll([
                'isDeleted' => [ '$ne' => true ]
            ])->toArray());
            
            return $response->withStatus(200)->write(
                json_encode($brands)
            );
        }
        
        public function get($request, $response, $args) 
        {
            $brand = \Models\Brand::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);
            
            if (empty($brand)) {
                return $response->withStatus(404)->write(
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
        
        public function update($request, $response, $args) 
        {
            $params = $request->getParams();
            
            if (empty($params['title']) || empty($params['pictures'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
                    ])
                );
            }
            
            $brand = \Models\Brand::fetchOne([ 
                'id' => $args['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);
            
            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Брэнд не найден'
                    ])
                );
            }
            
            $brand->title = $params['title'];
            $pictures = $brand->pictures;
            $brand->pictures = array_map(function($picture) { return $picture['id']; }, $params['pictures']);
            $brand->pictureId = $params['pictureId'];
            $brand->isDeleted = filter_var($params['isDeleted'], FILTER_VALIDATE_BOOLEAN);
            $brand->save();
            
            return $response->write(
                json_encode(
                    array_merge(
                        $brand->toArray(), 
                        [ 'pictures' => $pictures ]
                    )
                )
            );
        }
        
        public function remove($request, $response, $args) 
        {
            $brand = \Models\Brand::fetchOne([ 
                'id' => $args['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);
            
            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Брэнд не найден'
                    ])
                );
            }
            
            $brand->isDeleted = true;
            $brand->save();
            
            return $response->write(
                json_encode([
                    'success' => true
                ])
            );
        }
        
        public function addPicture($request, $response) 
        {
            $params = $request->getParams();
            
            $brand = \Models\Brand::fetchOne([
                'id' => $params['brand']['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);
            
            if (empty($brand)) {
                $brand = new \Models\Brand();
            }
            
            if (empty($params['picture']['id'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Изображение брэнда не задано'
                    ])
                );
            }
            
            $brand->title = $params['brand']['title'];
            $pictures = $brand->pictures ?? [];
            $pictures[] = $params['picture']['id'];
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