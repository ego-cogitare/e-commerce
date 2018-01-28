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
            
            $selectedPicture = null;
            $pictures = [];
            if (count($category->pictures) > 0) {
                foreach ($category->pictures as $pictureId) {
                    $picture = \Models\Media::fetchOne([ 
                        'id' => $pictureId,
                        'isDeleted' => [
                            '$ne' => true
                        ]
                    ]);
                    if ($picture) {
                        $pictures[] = $picture->toArray();
                        if ($picture->id === $category->pictureId) {
                            $selectedPicture = $picture->toArray();
                        }
                    }
                }
            }

            return $response->write(
                json_encode(array_merge(
                    $category->toArray(),
                    ['picture' => $selectedPicture ?? $pictures[0]],
                    ['pictures' => $pictures]
                ))
            );
        }
    }
