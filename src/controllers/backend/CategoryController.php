<?php
    namespace Controllers;

    class CategoryController
    {
        private $categoryTree = [];
        private $keyPrefix = 'categories-container::';
        private $settings;

        public function __construct($rdb)
        {
            $this->settings = $rdb->get('settings');
        }

        private function _fetchBranch($categories)
        {

            foreach ($categories as $category)
            {
                if (empty($category['parrentId']))
                {
                    $this->categoryTree[] = array_merge(
                      $category,
                      [
                        'module' => $category['title'],
                        'children' => $this->keyPrefix . $category['id']
                      ]
                    );
                }
                else
                {
                    array_walk_recursive($this->categoryTree, function(&$value) use ($categories, $category) {
                        if ($value === $this->keyPrefix . $category['parrentId']) {
                            $value = $categories;
                        }
                    });
                    //break;
                }

               /**
                * Looking for children categories
                */
                $categories = \Models\Category::fetchAll([
                    'isDeleted' => [
                        '$ne' => true
                    ],
                    'type' => 'final',
                    'parrentId' => $category['id']
                ], [ 'order' => 1 ]);
                
                // Expand all results with pictures models
                foreach ($categories as $category) {
                    $category = $category->expand();
                }

                $categories = array_map(function($category) {
                    return array_merge(
                        $category,
                        [
                            'module' => $category['title'],
                            'children' => $this->keyPrefix . $category['id']
                        ]
                    );
                }, $categories->toArray());

                $this->_fetchBranch($categories);
            }
        }

        public function index($request, $response)
        {
            $limit = $request->getParam('limit');
            $offset = $request->getParam('offset');

            $categories = \Models\Category::fetchAll([
                'isDeleted' => [
                    '$ne' => true
                ],
                'type' => 'final',
            ], [ 'order' => 1 ]);

            return $response->write(
                json_encode($categories->toArray())
            );
        }
        
        public function bootstrap($request, $response)
        {
            $bootstrap = \Models\Category::fetchOne([
                'isDeleted' => [
                    '$ne' => true,
                ],
                'type' => 'bootstrap'
            ]);

            if (empty($bootstrap)) {
                $bootstrap = \Models\Category::getBootstrap();
                $bootstrap->save();
            }
            
            return $response->write(
                json_encode($bootstrap->expand()->toArray())
            );
        }

        private function _fetchTree()
        {
          $this->categoryTree = [];

          $categories = \Models\Category::fetchAll([
              'isDeleted' => [
                  '$ne' => true
              ],
              'type' => 'final',
              'parrentId' => ''
          ], [ 'order' => 1 ]);

          foreach ($categories as $category) {
              $this->_fetchBranch([$category->expand()->toArray()]);
          }

          array_walk_recursive($this->categoryTree, function(&$value) {
              if (preg_match("/^{$this->keyPrefix}\w+$/", $value)) {
                  $value = [];
              }
          });

          return $this->categoryTree;
        }

        public function __invoke($request, $response)
        {
            $params = $request->getParams();
            
            $path = explode('/', trim($request->getUri()->getPath(), '/'));

            switch (array_pop($path)) {
                case 'tree':
                    // Get category tree
                    if ($request->isGet())
                    {
                      return $response->write(
                          json_encode($this->_fetchTree())
                      );
                    }

                    // Update category tree
                    if ($request->isPost())
                    {
                        $tree = json_decode($request->getParam('tree'), true);

                        // Recursively update tree
                        function walker($root)
                        {
                            if (count($root['children']) > 0)
                            {
                                foreach ($root['children'] as $children)
                                {
                                    $branch = \Models\Category::fetchOne([
                                        'id' => $children['id']
                                    ]);

                                    if (!empty($branch))
                                    {
                                        $branch->parrentId = $children['parrentId'];
                                        $branch->order = $children['order'];
                                        $branch->save();
                                    }

                                    walker($children);
                                }
                            }
                        }

                        walker($tree);

                        return $response->write(
                            json_encode($this->_fetchTree())
                        );
                    }
                break;

                case 'delete-picture':
                    $category = \Models\Category::fetchOne([
                        'id' => $params['categoryId'],
                        'isDeleted' => [
                            '$ne' => true
                        ]
                    ]);

                    if (empty($category)) {
                        return $response->withStatus(400)->write(
                            json_encode([ 'error' => 'Категория не найдена' ])
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
                    $category->pictures = array_values(array_filter(
                        $category->pictures,
                        function($pictureId) use ($picture) {
                            return $pictureId !== $picture->id;
                        }
                    ));

                    // If active brand picture deleted
                    if ($category->pictureId === $picture->id)
                    {
                        $category->pictureId = '';
                    }

                    // Update category settings
                    $category->save();

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

        public function get($request, $response, $args)
        {
            $category = \Models\Category::fetchOne([
                'id' => $args['id'],
                'type' => 'final',
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

            return $response->write(
                json_encode($category->toArray())
            );
        }

        public function update($request, $response, $args)
        {
            $params = $request->getParams();

            if (empty($params['title'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
                    ])
                );
            }

            $category = \Models\Category::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($category)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Категория не найдена'
                    ])
                );
            }
            
            $category->parrentId = $params['parrentId'];
            $category->title = $params['title'];
            $category->description = $params['description'];
            $category->isHidden = filter_var($params['isHidden'], FILTER_VALIDATE_BOOLEAN);
            $category->discount = $params['discount'];
            $category->discountType = $params['discountType'];
            $category->pictures = empty($params['pictures']) ? [] : $params['pictures'];
            $category->pictureId = $params['pictureId'];
            $category->type = 'final';
            $category->save();

            return $response->write(
                json_encode($category->toArray())
            );
        }

        public function remove($request, $response, $args)
        {
            $category = \Models\Category::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($category)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Категория не найдена'
                    ])
                );
            }

            $category->isDeleted = true;
            $category->save();

            return $response->write(
                json_encode([
                    'success' => true
                ])
            );
        }

        public function addPicture($request, $response, $args)
        {
            $params = $request->getParams();

            $category = \Models\Category::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($category)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Категория не найдена'
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

            $pictures = $category->pictures ?? [];
            $pictures[] = $params['picture']['id'];
            $category->pictures = $pictures;
            $category->save();

            return $response->write(
                json_encode($category->expand()->toArray())
            );
        }
    }
