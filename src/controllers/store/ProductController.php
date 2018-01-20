<?php
    namespace Controllers\Store;

    class ProductController
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
            
            $data = \Models\Product::fetchAll(
                $query, 
                $order, 
                $limit, 
                $offset
            )->toArray();
            
            $products = array_map(
                function($product) {
                    $picture = \Models\Media::fetchOne([
                        'id' => $product['pictureId'] 
                    ]);
                    if (!empty($picture)) {
                        $product['picture'] = $picture->toArray();
                    }
                    
                    $brand = \Models\Brand::fetchOne([
                        'id' => $product['brandId']
                    ]);
                    if (!empty($brand)) {
                        $product['brand'] = $brand->toArray();
                    }
                    
                    $category = \Models\Category::fetchOne([
                        'id' => $product['categoryId']
                    ]);
                    if (!empty($category)) {
                        $product['category'] = $category->toArray();
                    }
                    
                    return $product;
                },
                $data
            );

            return $response->write(
                json_encode($products)
            );
        }
    }
