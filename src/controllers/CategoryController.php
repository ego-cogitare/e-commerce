<?php
    namespace Controllers;

    class CategoryController
    {
        private $categoryTree = [];
        private $keyPrefix = 'categories-container::';

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
                   'parrentId' => $category['id']
                ], [ 'order' => 1 ]);

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
                ]
            ], [ 'order' => 1 ]);

            return $response->write(
                json_encode($categories->toArray())
            );
        }

        private function _fetchTree()
        {
          $this->categoryTree = [];

          $categories = \Models\Category::fetchAll([
              'isDeleted' => [
                  '$ne' => true
              ],
              'parrentId' => ''
          ], [ 'order' => 1 ]);

          foreach ($categories as $category) {
              $this->_fetchBranch([$category->toArray()]);
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
                        'id' => $params['id'],
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

        public function get($request, $response, $args)
        {
            $category = \Models\Category::fetchOne([
                'id' => $args['id'],
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

        public function add($request, $response)
        {
            $params = $request->getParams();

            if (empty($params['title'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
                    ])
                );
            }

            $category = new \Models\Category();
            $category->parrentId = $params['parrentId'];
            $category->title = $params['title'];
            $category->description = $params['description'];
            $category->isHidden = filter_var($params['isHidden'], FILTER_VALIDATE_BOOLEAN);
            $category->discount = $params['discount'];
            $category->discountType = $params['discountType'];
            $category->order = 999;
            $category->save();

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
