<?php
    namespace Controllers\Store;

    class ProductController
    {
        public function index($request, $response)
        {
            $limit      = intval($request->getParam('limit')) ?? null;
            $offset     = intval($request->getParam('offset')) ?? null;
            $filter     = $request->getParam('filter');
            $ascDesc    = intval($request->getParam('ascdesc'));
            $orderBy    = $request->getParam('orderBy');

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
            
            // Looking for keyword in filters
            if (!empty($query['$and'])) {
                foreach ($query['$and'] as &$item) {
                    if (!empty($item['keyword'])) {
                        $item['title'] = new \MongoDB\BSON\Regex($item['keyword'], 'i');
                        unset($item['keyword']);
                    }
                }
            }
            
            $order = empty($orderBy) ? null : [ $orderBy => $ascDesc ];
            
            $data = \Models\Product::fetchAll(
                $query, 
                $order, 
                $limit, 
                $offset
            );
            
            $products = [];
            foreach ($data as $product) {
                $products[] = $product->apiModel();
            }

            return $response->write(
                json_encode($products)
            );
        }
        
        public function get($request, $response, $args)
        {
            $product = \Models\Product::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($product)) {
                return $response->withStatus(404)->write(
                    json_encode([
                        'error' => 'Товар не найден'
                    ])
                );
            }

            return $response->write(
                json_encode($product->apiModel())
            );
        }
    }
