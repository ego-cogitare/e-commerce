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
            )->toArray();
            
            $brands = array_map(
                function($brand) {
                    $picture = \Models\Media::fetchOne([ 
                        'id' => $brand['pictureId'] 
                    ]);
                    if (empty($picture)) {
                        return $brand;
                    }
                    $brand['picture'] = $picture->toArray();
                    
                    return $brand;
                },
                $data
            );

            return $response->write(
                json_encode($brands)
            );
        }
    }
