<?php
    namespace Controllers\Backend;

    class CommentController
    {
        public function index($request, $response, $args)
        {
            if (empty($args['postId'])) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Не указан идентификатор поста.' ])
                );
            }
            
            $comments = \Models\PostComment::fetchAll([
                'isDeleted' => ['$ne' => true],
                'postId' => $args['postId']
            ], ['dateCreated' => -1])
            ->toArray();
            
            return $response->withStatus(200)->write(
                json_encode($comments)
            );
        }
        
        /*
         * Set approved reviews for the product
         */
        public function setApproved($request, $response, $args) 
        {
            $commentIds = $request->getParam('commentIds') ?? [];
            
            if (empty($args['postId'])) {
                return $response->withStatus(400)->write(
                    json_encode(['error' => 'Не указан идентификатор поста.'])
                );
            }
            
            $comments = \Models\PostComment::fetchAll([
                'postId' => $args['postId'],
                'isDeleted' => [
                    '$ne' => true 
                ]
            ]);
            
            foreach ($comments as $comment) {
                $comment->isApproved = in_array($comment->id, $commentIds);
                $comment->save();
            }
            
            return $response->write(
                json_encode(['success' => true])
            );
        }
    }