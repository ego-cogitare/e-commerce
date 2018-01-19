<?php
    namespace Controllers\Backend;

    class TagsController
    {
        public function index($request, $response)
        {
            $tags = \Models\Tag::fetchAll([ 'isDeleted' => [ '$ne' => true ] ])
                ->toArray();
            
            return $response->withStatus(200)->write(
                json_encode($tags)
            );
        }
        
        public function get($request, $response, $args) 
        {
            $tag = \Models\Tag::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);
            
            if (empty($tag)) {
                return $response->withStatus(404)->write(
                    json_encode([ 'error' => 'Тег не найден' ])
                );
            }
            
            return $response->write(
                json_encode($tag->toArray())
            );
        }
        
        public function add($request, $response) 
        {
            $params = $request->getParams();
            
            if (empty($params['title'])) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Не заполнено одно из обязательных полей' ])
                );
            }
            
            $tag = new \Models\Tag();
            $tag->title = $params['title'];
            $tag->dateCreated = time();
            $tag->isDeleted = false;
            $tag->save();
            
            return $response->write(
                json_encode($tag->toArray())
            );
        }
        
        public function remove($request, $response, $args) 
        {
            $tag = \Models\Tag::fetchOne([ 
                'id' => $args['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);
            
            if (empty($tag)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Тег не найден' ])
                );
            }
            
            $tag->isDeleted = true;
            $tag->save();
            
            return $response->write(
                json_encode([ 'success' => true ])
            );
        }
        
    }