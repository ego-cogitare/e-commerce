<?php
    namespace Controllers\Store;

    class CategoryController
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
            
            $data = \Models\Category::fetchAll(
                $query, 
                $order, 
                $limit, 
                $offset
            );
            
            $categories = [];
            
            if (!empty($data)) {
                foreach ($data as $category) {
                    $categories[] = $category->expand()->toArray();
                }
            }
            
            return $response->write(
                json_encode($categories)
            );
        }
    }
