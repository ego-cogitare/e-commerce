<?php
    namespace Controllers\Store;

    class BlogController
    {
        public function index($request, $response)
        {
            $limit   = intval($request->getParam('limit')) ?? null;
            $offset  = intval($request->getParam('offset')) ?? null;
            $ascDesc = intval($request->getParam('ascdesc'));
            $orderBy = $request->getParam('orderBy');
            $filter  = $request->getParam('filter');

            $query = [ 
                'isDeleted' => [ 
                    '$ne' => true 
                ],
                'type' => 'final'
            ];
            
            if (!empty($filter)) {
                $query = array_merge(
                    $query, json_decode($filter, true)
                );
            }
            
            $order = empty($orderBy) 
                ? null 
                : [ $orderBy => $ascDesc ];
            
            $data = \Models\Post::fetchAll(
                $query, 
                $order, 
                $limit, 
                $offset
            );
            
            $posts = [];
            
            if (!empty($data)) {
                foreach ($data as $post) {
                    $posts[] = $post->apiModel(['pictures', /*'descriptions'*/]);
                }
            }
            
            return $response->write(
                json_encode($posts)
            );
        }
        
        public function get($request, $response, $args)
        {
            $post = \Models\Post::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($post)) {
                return $response->withStatus(404)->write(
                    json_encode([
                        'error' => 'Пост не найден'
                    ])
                );
            }

            return $response->write(
                json_encode($post->apiModel())
            );
        }
    }
