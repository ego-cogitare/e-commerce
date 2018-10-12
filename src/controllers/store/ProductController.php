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
                $products[] = $product->apiModel(1, 0, ['descriptions', 'relatedProducts', 'properties', 'pictures', 'category', 'reviews']);
            }

            return $response->write(
                json_encode($products)
            );
        }
        
        public function get($request, $response, $args)
        {
            $product = \Models\Product::fetchOne([
                'slug' => $args['id'],
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
        
        public function addReview($request, $response)
        {
            $productId = $request->getParam('productId');
            $rate      = intval($request->getParam('rate'));
            $userName  = $request->getParam('userName');
            $message   = $request->getParam('review');

            if (empty($productId)) {
                return $response->withStatus(400)->write(
                    json_encode(['success' => false, 'error' => 'Товар не указан.'])
                );
            }
            
            $product = \Models\Product::fetchOne([
                'id' => $productId,
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($product) ) {
                return $response->withStatus(404)->write(
                    json_encode([
                        'error' => 'Товар не найден.'
                    ])
                );
            }
            
            if (empty($rate) || $rate < 1 || $rate > 5) {
                return $response->withStatus(400)->write(
                    json_encode(['success' => false, 'error' => 'Оценка не указана.'])
                );
            }
                        
            if (empty($userName)) {
                return $response->withStatus(400)->write(
                    json_encode(['success' => false, 'error' => 'Укажите Ваше имя.'])
                );
            }
            
            if (strlen($userName) > 100) {
                return $response->withStatus(400)->write(
                    json_encode(['success' => false, 'error' => 'Ограничение длины имени 100 символов.'])
                );
            }
            
            if (empty($message)) {
                return $response->withStatus(400)->write(
                    json_encode(['success' => false, 'error' => 'Поле отзыва не может быть пустым.'])
                );
            }
            
            if (strlen($message) > 1000) {
                return $response->withStatus(400)->write(
                    json_encode(['success' => false, 'error' => 'Ограничение длины отзыва 1000 символов.'])
                );
            }
            
            $review = new \Models\ProductReview();
            $review->productId = $productId;
            $review->rate = $rate;
            $review->userName = $userName;
            $review->review = $message;
            $review->dateCreated = time();
            $review->isApproved = false;
            $review->isDeleted = false;
            $review->save();
            
            return $response->write(
                json_encode(['success' => true, 'message' => 'Спасибо за Ваш отзыв.'])
            );
        }
    }
