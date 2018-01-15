<?php
    namespace Controllers\Backend;

    class BrandController
    {
        public function index($request, $response)
        {
            $limit = $request->getParam('limit');
            $offset = $request->getParam('offset');

            $brands = array_map(
                function($brand) {
                    $brand['pictures'] = array_map(
                        function($pictureId) {
                            return \Models\Media::fetchOne(['id' => $pictureId])->toArray();
                        },
                        $brand['pictures']
                    );
                    return $brand;
                },
                \Models\Brand::fetchAll([ 'isDeleted' => [ '$ne' => true ] ])->toArray()
            );

            return $response->withStatus(200)->write(
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

            $pictures = $brand->pictures ?? [];
            $pictures = array_map(function($id) {
                return \Models\Media::fetchOne([ 'id' => $id ])->toArray();
            }, $pictures);
            $brand->pictures = $pictures;

            return $response->write(
                json_encode($brand->toArray())
            );
        }

        public function add($request, $response)
        {
            $params = $request->getParams();

            if (empty($params['title']) && empty($params['pictures'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
                    ])
                );
            }

            $brand = new \Models\Brand();
            $brand->title = $params['title'];
            $brand->save();

            return $response->write(
                json_encode($brand->toArray())
            );
        }

        public function update($request, $response, $args)
        {
            $params = $request->getParams();

            if (empty($params['title']) || empty($params['pictures'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
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

            $brand->title = $params['title'];
            $pictures = $brand->pictures;
            $brand->pictures = array_map(function($picture) { return $picture['id']; }, $params['pictures']);
            $brand->pictureId = $params['pictureId'];
            $brand->isDeleted = filter_var($params['isDeleted'], FILTER_VALIDATE_BOOLEAN);
            $brand->save();

            return $response->write(
                json_encode(
                    array_merge(
                        $brand->toArray(),
                        [ 'pictures' => $pictures ]
                    )
                )
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
                json_encode([
                    'success' => true
                ])
            );
        }

        public function addPicture($request, $response)
        {
            $params = $request->getParams();

            $brand = \Models\Brand::fetchOne([
                'id' => $params['brand']['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($brand)) {
                $brand = new \Models\Brand();
            }

            if (empty($params['picture']['id'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Изображение брэнда не задано'
                    ])
                );
            }

            $brand->title = $params['brand']['title'];
            $pictures = $brand->pictures ?? [];
            $pictures[] = $params['picture']['id'];
            $brand->pictures = $pictures;
            $brand->save();

            $brand->pictures = array_map(function($id) {
                return \Models\Media::fetchOne([ 'id' => $id ])->toArray();
            }, $pictures);

            return $response->write(
                json_encode($brand->toArray())
            );
        }

        public function deletePicture($request, $response, $args)
        {
            global $app;

            $params = $request->getParams();

            $brand = \Models\Brand::fetchOne([
                'id' => $params['brandId'],
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
