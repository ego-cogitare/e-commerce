<?php
    namespace Controllers;
    
    class CategoryController
    {
        public function index($request, $response)
        {
            $limit = $request->getParam('limit');
            $offset = $request->getParam('offset');
            
            $categories = \Models\Category::fetchAll([
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            return $response->write(
                json_encode($categories->toArray())
            );
        }
        
        public function get($request, $response, $args)
        {
            $category = \Models\Category::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);

            if (empty($category)) {
                return $response->withStatus(404)->write(
                    json_encode([
                        'error' => 'Категория не найдена'
                    ])
                );
            }

            return $response->write(
                json_encode($category->toArray())
            );
        }
        
        public function add($request, $response) 
        {
            $params = $request->getParams();
            
            if (empty($params['title'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
                    ])
                );
            }
            
            $category = new \Models\Category();
            $category->title = $params['title'];
            $category->description = $params['description'];
            $category->save();
            
            return $response->write(
                json_encode($category->toArray())
            );
        }
    }