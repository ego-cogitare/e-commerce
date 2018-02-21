<?php
    namespace Controllers\Backend;

    class ProductController
    {
        private $settings;

        public function __construct($rdb)
        {
            $this->settings = $rdb->get('settings');
        }

        public function __invoke($request, $response, $args)
        {
            $params = $request->getParams();

            // Get controller action
            $path = explode('/', trim($request->getUri()->getPath(), '/'));
            $action = array_pop($path);

            switch ($action)
            {
                case 'delete-picture':
                case 'delete-award':
                    $product = \Models\Product::fetchOne([
                        'id' => $params['productId'],
                        'isDeleted' => [
                            '$ne' => true
                        ]
                    ]);

                    if (empty($product)) {
                        return $response->withStatus(400)->write(
                            json_encode([ 'error' => 'Товар не найден' ])
                        );
                    }

                    $picture = \Models\Media::fetchOne([
                        'id' => $params['id'],
                        /*'isDeleted' => [
                            '$ne' => true
                        ]*/
                    ]);

                    if (empty($picture)) {
                        return $response->withStatus(400)->write(
                            json_encode([ 'error' => 'Изображение не найдено' ])
                        );
                    }

                    // Mark picture as deleted
                    $picture->isDeleted = true;
                    $picture->save();
                    
                    if ($action === 'delete-picture')
                    {
                        // Remove pictureId from product pictures list
                        $product->pictures = array_values(array_filter(
                            $product->pictures,
                            function($pictureId) use ($picture) {
                                return $pictureId !== $picture->id;
                            }
                        ));

                        // If active brand picture deleted
                        if ($product->pictureId === $picture->id)
                        {
                            $product->pictureId = '';
                        }
                    }
                    else
                    {
                        // Remove pictureId from awards pictures list
                        $product->awards = array_values(array_filter(
                            $product->awards,
                            function($pictureId) use ($picture) {
                                return $pictureId !== $picture->id;
                            }
                        ));
                    }

                    // Update brand settings
                    $product->save();

                    // Get picture path
                    $picturePath = $this->settings['files']['upload']['directory'] . '/'
                      . $picture->path . '/' . $picture->name;

                    // Delete picture
                    if (unlink($picturePath))
                    {
                        return $response->write(
                            json_encode([ 'success' => true ])
                        );
                    }
                    else
                    {
                        return $response->withStatus(400)->write(
                            json_encode([ 'error' => 'Изображение не найдено' ])
                        );
                    }
                break;
            }
        }

        public function index($request, $response)
        {
            $params = $request->getParams();

            $query = [
                'isDeleted' => [
                    '$ne' => true
                ],
                'type' => 'final'
            ];

            if (isset($params['filter'])) {
                $query = array_merge($query, $params['filter']);
            }

            $sort = null;

            if (isset($params['sort'])) {
                $sort = array_map('intval', $params['sort']);
            }

            $products = [];

            foreach (\Models\Product::fetchAll($query, $sort) as $product) {
                $products[] = $product->apiModel(1, 0, [
                    'brand', 'category', 'relativeProducts', 
                    'pictures', 'properties', 'reviews'
                ]);
            }

            return $response->write(
                json_encode($products)
            );
        }

        public function bootstrap($request, $response)
        {
            $bootstrap = \Models\Product::fetchOne([
                'isDeleted' => [
                    '$ne' => true,
                ],
                'type' => 'bootstrap'
            ]);

            if (empty($bootstrap)) {
                $bootstrap = \Models\Product::getBootstrap();
                $bootstrap->save();
            }

            return $response->write(
                json_encode($bootstrap->expand()->toArray())
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
                json_encode($product->expand()->toArray())
            );
        }

        public function update($request, $response, $args)
        {
            $params = $request->getParams();

            if (empty($params['title']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено название.'
                    ])
                );
            }

            if (empty($params['sku']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнен артикул.'
                    ])
                );
            }

            if (empty($params['briefly']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено краткое описание.'
                    ])
                );
            }

            if (empty($params['description']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено описание.'
                    ])
                );
            }

            if (empty($params['brandId']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнен брэнд.'
                    ])
                );
            }

            if (empty($params['categoryId']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнена категория.'
                    ])
                );
            }

            if (empty($params['pictures']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено изображение.'
                    ])
                );
            }

            if (empty($params['price']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнена цена.'
                    ])
                );
            }

            if (!empty($params['discountTimeout']) && !preg_match('/^\d+$/', $params['discountTimeout']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Время действия скидки задано неверно.'
                    ])
                );
            }

            $product = \Models\Product::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($product)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Продукт не найден'
                    ])
                );
            }

            $product->type = 'final';
            $product->title = $params['title'];
            $product->briefly = $params['briefly'];
            $product->description = $params['description'];
            $product->isAvailable = filter_var($params['isAvailable'], FILTER_VALIDATE_BOOLEAN);
            $product->isAuction = filter_var($params['isAuction'], FILTER_VALIDATE_BOOLEAN);
            $product->isNovelty = filter_var($params['isNovelty'], FILTER_VALIDATE_BOOLEAN);
            $product->isBestseller = filter_var($params['isBestseller'], FILTER_VALIDATE_BOOLEAN);
            $product->categoryId = $params['categoryId'];
            $product->brandId = $params['brandId'];
            $product->relatedProducts = $params['relatedProducts'] ?? [];
            $product->pictures = $params['pictures'] ?? [];
            $product->pictureId = $params['pictureId'];
            $product->awards = $params['awards'] ?? [];
            $product->properties = $params['properties'] ?? [];
            $product->price = filter_var($params['price'], FILTER_VALIDATE_FLOAT);
            $product->discount = filter_var($params['discount'], FILTER_VALIDATE_FLOAT);
            $product->discountType = $params['discountType'];
            $product->discountTimeout = filter_var($params['discountTimeout'], FILTER_VALIDATE_INT);
            $product->sku = $params['sku'];
            $product->video = $params['video'];
            $product->isDeleted = false;
            $product->dateCreated = time();
            $product->save();

            return $response->write(
                json_encode($product->expand()->toArray())
            );
        }

        public function remove($request, $response, $args)
        {
            $product = \Models\Product::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($product)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Продукт не найден'
                    ])
                );
            }

            $product->isDeleted = true;
            $product->save();

            return $response->write(
                json_encode([
                    'success' => true
                ])
            );
        }

        public function addPicture($request, $response, $args)
        {
            $params = $request->getParams();

            $product = \Models\Product::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($product)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Продукт не найден'
                    ])
                );
            }

            if (empty($params['picture']['id'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Изображение не задано'
                    ])
                );
            }

            $pictures = $product->pictures ?? [];
            $pictures[] = $params['picture']['id'];
            $product->pictures = $pictures;
            $product->save();

            return $response->write(
                json_encode($product->expand()->toArray())
            );
        }
        
        public function addAward($request, $response, $args)
        {
            $params = $request->getParams();

            $product = \Models\Product::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($product)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Продукт не найден'
                    ])
                );
            }

            if (empty($params['picture']['id'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Изображение не задано'
                    ])
                );
            }

            $pictures = $product->awards ?? [];
            $pictures[] = $params['picture']['id'];
            $product->awards = $pictures;
            $product->save();

            return $response->write(
                json_encode($product->expand()->toArray())
            );
        }
        
        public function properties($request, $response) 
        {
            $properties = \Models\ProductProperty::fetchAll([
                'isDeleted' => [
                    '$ne' => true
                ],
                'parentId' => ''
            ])->toArray();
            
            // Extend property with child properties (property values)
            if (count($properties) > 0) 
            {
                foreach ($properties as &$property) 
                {
                    $children = \Models\ProductProperty::fetchAll([
                        'isDeleted' => [
                            '$ne' => true
                        ],
                        'parentId' => $property['id']
                    ])->toArray();
                    
                    $property = array_merge($property, ['children' => $children]);
                }
            }

            return $response->write(
                json_encode($properties)
            );
        }
        
        public function addProperty($request, $response, $args) 
        {
            $params = $request->getParams();

            if (empty($params['key']))
            {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено название свойства.'
                    ])
                );
            }
            
            if (!empty($params['parentId']))
            {
                $parentProperty = \Models\ProductProperty::fetchOne([
                    'id' => $params['parentId'],
                    'isDeleted' => [
                        '$ne' => true
                    ]
                ]);

                if (empty($parentProperty)) 
                {
                    return $response->withStatus(400)->write(
                        json_encode([
                            'error' => 'Свойство-родитель не найдено'
                        ])
                    );
                }
            }
            
            $property = new \Models\ProductProperty();
            $property->key = $params['key'];
            $property->parentId = $params['parentId'];
            $property->isDeleted = false;
            $property->save();

            return $response->write(
                json_encode($property->toArray())
            );
        }
        
        public function updateProperty($request, $response, $args) 
        {
            $params = $request->getParams();

            if (empty($params['key'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено название свойства.'
                    ])
                );
            }

            $property = \Models\ProductProperty::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($property)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Свойство не найдено'
                    ])
                );
            }
            
            if (!empty($params['parentId']))
            {
                $parentProperty = \Models\ProductProperty::fetchOne([
                    'id' => $params['parentId'],
                    'isDeleted' => [
                        '$ne' => true
                    ]
                ]);

                if (empty($parentProperty)) 
                {
                    return $response->withStatus(400)->write(
                        json_encode([
                            'error' => 'Свойство-родитель не найдено'
                        ])
                    );
                }
            }
            
            $property->key = $params['key'];
            $property->parentId = $params['parentId'];
            $property->save();

            return $response->write(
                json_encode($property->toArray())
            );
        }
        
        public function removeProperty($request, $response, $args) 
        {
            $property = \Models\ProductProperty::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);
            
            if (empty($property)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Свойство не найдено'
                    ])
                );
            }

            $property->isDeleted = true;
            $property->save();

            return $response->write(
                json_encode([
                    'success' => true
                ])
            );
        }
    }
