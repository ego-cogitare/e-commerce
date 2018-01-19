<?php
    namespace Controllers\Backend;

    class BlogController
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

            switch (array_pop($path))
            {
                case 'delete-picture':
                    $post = \Models\Post::fetchOne([
                        'id' => $params['postId'],
                        'isDeleted' => [
                            '$ne' => true
                        ]
                    ]);

                    if (empty($post)) {
                        return $response->withStatus(400)->write(
                            json_encode([ 'error' => 'Пост не найден' ])
                        );
                    }

                    $picture = \Models\Media::fetchOne([
                        'id' => $params['id'],
                    ]);

                    if (empty($picture)) {
                        return $response->withStatus(400)->write(
                            json_encode([ 'error' => 'Изображение не найдено' ])
                        );
                    }

                    // Mark picture as deleted
                    $picture->isDeleted = true;
                    $picture->save();

                    // Remove pictureId from brand pictures list
                    $post->pictures = array_values(array_filter(
                        $post->pictures,
                        function($pictureId) use ($picture) {
                            return $pictureId !== $picture->id;
                        }
                    ));

                    // If active brand picture deleted
                    if ($post->pictureId === $picture->id)
                    {
                        $post->pictureId = '';
                    }

                    // Update post settings
                    $post->save();

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

            $posts = [];

            foreach (\Models\Post::fetchAll($query) as $post) {
                $posts[] = $post->expand()->toArray();
            }

            return $response->write(
                json_encode($posts)
            );
        }
        
        public function bootstrap($request, $response)
        {
            $bootstrap = \Models\Post::fetchOne([
                'isDeleted' => [
                    '$ne' => true,
                ],
                'type' => 'bootstrap'
            ]);

            if (empty($bootstrap)) {
                $bootstrap = \Models\Post::getBootstrap();
                $bootstrap->save();
            }

            return $response->write(
                json_encode($bootstrap->expand()->toArray())
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
                json_encode($post->expand()->toArray())
            );
        }
        
        public function addPicture($request, $response, $args)
        {
            $params = $request->getParams();

            $post = \Models\Post::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($post)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Пост не найден'
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

            $pictures = $post->pictures ?? [];
            $pictures[] = $params['picture']['id'];
            $post->pictures = $pictures;
            $post->save();

            return $response->write(
                json_encode($post->expand()->toArray())
            );
        }

        public function update($request, $response, $args)
        {
            $params = $request->getParams();

            if (empty($params['title']) || empty($params['briefly']) || empty($params['body'])) {
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
                    json_encode([ 'error' => 'Пост не найден' ])
                );
            }

            $post->type = 'final';
            $post->title = $params['title'];
            $post->briefly = $params['briefly'];
            $post->body = $params['body'];
            $post->tags = $params['tags'];
            $post->isVisible = filter_var($params['isVisible'], FILTER_VALIDATE_BOOLEAN);
            $post->save();

            return $response->write(
                json_encode($post->expand()->toArray())
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
