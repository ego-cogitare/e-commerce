<?php
    namespace Controllers;

    class MenuController
    {
        private $menuTree = [];
        private $keyPrefix = 'menu-container::';
        private $settings;

        public function __construct($rdb)
        {
            $this->settings = $rdb->get('settings');
        }

        private function _fetchBranch($items)
        {

            foreach ($items as $item)
            {
                if (empty($item['parrentId']))
                {
                    $this->menuTree = array_merge(
                      $item,
                      [
                        'module' => $item['title'],
                        'children' => $this->keyPrefix . $item['id']
                      ]
                    );
                }
                else
                {
                    array_walk_recursive($this->menuTree, function(&$value) use ($items, $item) {
                        if ($value === $this->keyPrefix . $item['parrentId']) {
                            $value = $items;
                        }
                    });
                }

               /**
                * Looking for children items
                */
                $items = \Models\Menu::fetchAll([
                    'isDeleted' => [
                        '$ne' => true
                    ],
                    'parrentId' => $item['id']
                ], [ 'order' => 1 ]);

                $items = array_map(function($item) {
                    return array_merge(
                        $item,
                        [
                            'module' => $item['title'],
                            'children' => $this->keyPrefix . $item['id']
                        ]
                    );
                }, $items->toArray());

                $this->_fetchBranch($items);
            }
        }

        public function index($request, $response)
        {
            $items = \Models\Menu::fetchAll([
                'isDeleted' => [
                    '$ne' => true
                ],
            ], [ 'order' => 1 ]);

            return $response->write(
                json_encode($items->toArray())
            );
        }

        private function _fetchTree($rootId)
        {
            $this->menuTree = [];

            $items = \Models\Menu::fetchAll([
                'isDeleted' => [
                    '$ne' => true
                ],
                'id' => $rootId
            ], [ 'order' => 1 ]);
            
            if (count($items) === 0) {
                throw new \Exception('Не найден корневой элемент меню: ' . $rootId);
            }

            foreach ($items as $item) {
                $this->_fetchBranch([$item->toArray()]);
            }

            array_walk_recursive($this->menuTree, function(&$value) {
                if (preg_match("/^{$this->keyPrefix}\w+$/", $value)) {
                    $value = [];
                }
            });

            return $this->menuTree;
        }

        public function __invoke($request, $response)
        {
            $params = $request->getParams();
            
            $path = explode('/', trim($request->getUri()->getPath(), '/'));
            
            // Get menu root item id
            $rootId = array_pop($path);

            switch (array_pop($path)) {
                case 'get':
                    // Get menu tree
                    if ($request->isGet())
                    {
                      return $response->write(
                          json_encode($this->_fetchTree($rootId))
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
                                    $branch = \Models\Menu::fetchOne([
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
                            json_encode($this->_fetchTree($rootId))
                        );
                    }
                break;
            }
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
    }
