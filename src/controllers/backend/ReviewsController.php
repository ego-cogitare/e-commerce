<?php
    namespace Controllers\Backend;

    class ReviewsController
    {
        public function index($request, $response, $args)
        {
            if (empty($args['productId'])) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Не указан идентификатор продукта.' ])
                );
            }
            
            $reviews = \Models\ProductReview::fetchAll([
                'isDeleted' => ['$ne' => true],
                'productId' => $args['productId']
            ], ['dateCreated' => -1])
            ->toArray();
            
            return $response->withStatus(200)->write(
                json_encode($reviews)
            );
        }
        
        /*
         * Set approved reviews for the product
         */
        public function setApproved($request, $response, $args) 
        {
            $reviewIds = $request->getParam('reviewIds') ?? [];
            
            if (empty($args['productId'])) {
                return $response->withStatus(400)->write(
                    json_encode(['error' => 'Не указан идентификатор продукта.'])
                );
            }
            
            $reviews = \Models\ProductReview::fetchAll([
                'productId' => $args['productId'],
                'isDeleted' => [
                    '$ne' => true 
                ]
            ]);
            
            foreach ($reviews as $review) {
                $review->isApproved = in_array($review->id, $reviewIds);
                $review->save();
            }
            
            return $response->write(
                json_encode(['success' => true])
            );
        }
    }