<?php
    namespace Controllers\Backend;

    class BlogController
    {
        public function index($request, $response)
        {
            $posts = \Models\Post::fetchAll([ 'isDeleted' => [ '$ne' => true ] ])
                ->toArray();

            return $response->withStatus(200)->write(
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
                    json_encode([ 'error' => 'Пост не найден' ])
                );
            }

            return $response->write(
                json_encode($post->toArray())
            );
        }

        public function add($request, $response)
        {
            $params = $request->getParams();

            if (empty($params['title']) && empty($params['body'])) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Не заполнено одно из обязательных полей' ])
                );
            }

            $post = new \Models\Post();
            $post->title = $params['title'];
            $post->body = $params['body'];
            $post->isVisible = filter_var($params['isVisible'], FILTER_VALIDATE_BOOLEAN);
            $post->dateCreated = time();
            $post->isDeleted = false;
            $post->save();

            return $response->write(
                json_encode($post->toArray())
            );
        }

        public function update($request, $response, $args)
        {
            $params = $request->getParams();

            if (empty($params['title']) || empty($params['body'])) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Не заполнено одно из обязательных полей' ])
                );
            }

            $post = \Models\Post::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($post)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Пост не найдена' ])
                );
            }

            $post->title = $params['title'];
            $post->body = $params['body'];
            $post->isVisible = filter_var($params['isVisible'], FILTER_VALIDATE_BOOLEAN);
            $post->save();

            return $response->write(
                json_encode($post->toArray())
            );
        }

        public function remove($request, $response, $args)
        {
            $post = \Models\Post::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($post)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Пост не найден' ])
                );
            }

            $post->isDeleted = true;
            $post->save();

            return $response->write(
                json_encode([ 'success' => true ])
            );
        }

    }
