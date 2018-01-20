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
                    $posts[] = $post->expand()->toArray();
                }
            }
            
            return $response->write(
                json_encode($posts)
            );
        }
    }
