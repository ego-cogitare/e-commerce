<?php
    namespace Controllers\Store;

    class BrandController
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
            
            $data = \Models\Brand::fetchAll(
                $query, 
                $order, 
                $limit, 
                $offset
            );
            
            $brands = [];
            foreach ($data as $item) {
                $brands[] = $item->apiModel(['pictures']);
            }

            return $response->write(
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
                        'error' => 'Бренд не найден'
                    ])
                );
            }

            return $response->write(
                json_encode($brand->apiModel())
            );
        }
    }
