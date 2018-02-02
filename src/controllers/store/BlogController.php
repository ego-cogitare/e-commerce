<?php
    namespace Controllers\Store;

    class BlogController
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
            
            $data = \Models\Post::fetchAll(
                $query, 
                $order, 
                $limit, 
                $offset
            );
            
            $posts = [];
            
            if (!empty($data)) {
                foreach ($data as $post) {
                    $posts[] = $post->apiModel(['pictures', /*'descriptions'*/]);
                }
            }
            
            return $response->write(
                json_encode($posts)
            );
        }
        
        public function get($request, $response, $args)
        {
            $post = \Models\Post::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($post)) {
                return $response->withStatus(404)->write(
                    json_encode([
                        'error' => 'Пост не найден'
                    ])
                );
            }

            return $response->write(
                json_encode($post->apiModel())
            );
        }
        
        public function addComment($request, $response)
        {
            $postId   = $request->getParam('postId');
            $userName = $request->getParam('userName');
            $message  = $request->getParam('message');

            if (empty($postId)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'success' => false, 
                        'error' => 'Пост не указан.'
                    ])
                );
            }
            
            $post = \Models\Post::fetchOne([
                'id' => $postId,
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($post) ) {
                return $response->withStatus(404)->write(
                    json_encode([
                        'error' => 'Пост не найден.'
                    ])
                );
            }
            
            if (empty($userName)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'success' => false, 
                        'field' => 'userName',
                        'error' => 'Укажите Ваше имя.'
                    ])
                );
            }
            
            if (strlen($userName) > 100) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'success' => false, 
                        'field' => 'userName',
                        'error' => 'Ограничение длины имени 100 символов.'
                    ])
                );
            }
            
            if (empty($message)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'success' => false,
                        'field' => 'message',
                        'error' => 'Поле комментария не может быть пустым.'
                    ])
                );
            }
            
            if (strlen($message) > 1000) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'success' => false,
                        'field' => 'message',
                        'error' => 'Ограничение длины комментария 1000 символов.'
                    ])
                );
            }
            
            $comment = new \Models\PostComment();
            $comment->postId = $postId;
            $comment->userName = $userName;
            $comment->comment = $message;
            $comment->dateCreated = time();
            $comment->isApproved = true;
            $comment->isDeleted = false;
            $comment->save();
            
            return $response->write(
                json_encode([
                    'success' => true, 
                    'comment' => $comment->toArray(),
                    'message' => 'Спасибо за Ваш комментарий.'
                ])
            );
        }
    }
