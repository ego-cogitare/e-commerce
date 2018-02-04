<?php
    namespace Controllers\Backend;

    class BrandController
    {
        public function index($request, $response)
        {
            $limit = $request->getParam('limit');
            $offset = $request->getParam('offset');

            $data = \Models\Brand::fetchAll([ 
                'isDeleted' => [ 
                    '$ne' => true 
                ],
                'type' => 'final'
            ]);
            
            $brands = [];
            foreach ($data as $item) {
                $brands[] = $item->apiModel();
            }

            return $response->write(
                json_encode($brands)
            );
        }

        public function get($request, $response, $args)
        {
            $brand = \Models\Brand::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($brand)) {
                return $response->withStatus(404)->write(
                    json_encode([
                        'error' => 'Брэнд не найден'
                    ])
                );
            }

            return $response->write(
                json_encode($brand->apiModel())
            );
        }
        
        public function bootstrap($request, $response)
        {
            $bootstrap = \Models\Brand::fetchOne([
                'isDeleted' => [
                    '$ne' => true,
                ],
                'type' => 'bootstrap'
            ]);

            if (empty($bootstrap)) {
                $bootstrap = \Models\Brand::getBootstrap();
                $bootstrap->save();
            }

            return $response->write(
                json_encode($bootstrap->apiModel())
            );
        }

        public function update($request, $response, $args)
        {
            $params = $request->getParams();

            if (empty($params['title'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено название брэнда'
                    ])
                );
            }

            if (empty($params['pictures'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не задано превью брэнда'
                    ])
                );
            }

            if (empty($params['covers'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не задан постер брэнда'
                    ])
                );
            }

            $brand = \Models\Brand::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Брэнд не найден'
                    ])
                );
            }

            $brand->type = 'final';
            $brand->title = $params['title'];
            $brand->pictures = $params['pictures'];
            $brand->pictureId = $params['pictureId'];
            $brand->covers = $params['covers'];
            $brand->coverId = $params['coverId'];
            $brand->isDeleted = filter_var($params['isDeleted'], FILTER_VALIDATE_BOOLEAN);
            $brand->save();

            return $response->write(
                json_encode($brand->apiModel())
            );
        }

        public function remove($request, $response, $args)
        {
            global $app;

            $brand = \Models\Brand::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Брэнд не найден'
                    ])
                );
            }

            // Delete all brand images
            if (count($brand->pictures) > 0)
            {
                array_walk(
                    $brand->pictures,
                    function ($pictureId) use ($app)
                    {
                        $picture = \Models\Media::fetchOne([
                            'id' => $pictureId
                        ]);

                        if (!empty($picture))
                        {
                            // Set picture state to "deleted" in database
                            $picture->isDeleted = true;
                            $picture->save();

                            // Get picture path
                            $picturePath = $app->getContainer()->settings['files']['upload']['directory'] . '/'
                              . $picture->path . '/' . $picture->name;

                            // Delete picture
                            unlink($picturePath);
                        }
                    }
                );
            }

            $brand->isDeleted = true;
            $brand->save();

            return $response->write(
                json_encode(['success' => true])
            );
        }

        public function addPicture($request, $response, $args)
        {
            $params = $request->getParams();
            
            if (empty($args['id'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Идентификатор брэнда не может быть пустым'
                    ])
                );
            }
            
            $brand = \Models\Brand::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);
            
            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Брэнд не найден'
                    ])
                );
            }
            
            if (empty($params['pictureId'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Превью изображение брэнда не задано'
                    ])
                );
            }

            $pictures = $brand->pictures;
            if (sizeof($pictures) > 0) {
                $pictures[] = $params['pictureId'];
            }
            else {
               $pictures = [$params['pictureId']]; 
            }
            $brand->pictures = $pictures;
            $brand->save();

            return $response->write(
                json_encode($brand->apiModel())
            );
        }

        public function deletePicture($request, $response, $args)
        {
            global $app;

            $params = $request->getParams();

            $brand = \Models\Brand::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Брэнд не найден' ])
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

            // Remove pictureId from brand pictures list
            $brand->pictures = array_values(array_filter(
                $brand->pictures,
                function($pictureId) use ($picture) {
                    return $pictureId !== $picture->id;
                }
            ));

            // If active brand picture deleted
            if ($brand->pictureId === $picture->id)
            {
                $brand->pictureId = '';
            }

            // Update brand settings
            $brand->save();

            // Get picture path
            $picturePath = $app->getContainer()->settings['files']['upload']['directory'] . '/'
              . $picture->path . '/' . $picture->name;

            // Delete picture
            if (unlink($picturePath))
            {
                return $response->write(
                    json_encode(['success' => true])
                );
            }
            else
            {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Изображение не найдено' ])
                );
            }
        }
        
        public function addCover($request, $response, $args)
        {
            $params = $request->getParams();
            
            if (empty($args['id'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Идентификатор брэнда не может быть пустым'
                    ])
                );
            }
            
            $brand = \Models\Brand::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);
            
            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Брэнд не найден'
                    ])
                );
            }
            
            if (empty($params['coverId'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Постер изображение брэнда не задано'
                    ])
                );
            }

            $covers = $brand->covers;
            if (sizeof($covers) > 0 ) {
                $covers[] = $params['coverId'];
            }
            else {
               $covers = [$params['coverId']]; 
            }
            $brand->covers = $covers;
            $brand->save();

            return $response->write(
                json_encode($brand->apiModel())
            );
        }

        public function deleteCover($request, $response, $args)
        {
            global $app;

            $params = $request->getParams();

            $brand = \Models\Brand::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($brand)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Брэнд не найден' ])
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

            // Remove pictureId from brand pictures list
            $brand->covers = array_values(array_filter(
                $brand->covers,
                function($pictureId) use ($picture) {
                    return $pictureId !== $picture->id;
                }
            ));

            // If active brand picture deleted
            if ($brand->coverId === $picture->id)
            {
                $brand->coverId = '';
            }

            // Update brand settings
            $brand->save();

            // Get picture path
            $picturePath = $app->getContainer()->settings['files']['upload']['directory'] . '/'
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
        }
    }
