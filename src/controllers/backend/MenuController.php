<?php
    namespace Controllers\Backend;
    
    use Components\MenuComponent;

    class MenuController
    {
        private $settings;

        public function __construct($rdb)
        {
            $this->settings = $rdb->get('settings');
        }

        public function index($request, $response)
        {
            $items = \Models\Menu::fetchAll([
                '$and' => [
                    ['isDeleted' => ['$ne' => true]],
                    ['$or' => [
                        ['parrentId' => '' ],
                        ['parrentId' => [ '$exists' => false ]]
                    ]]
                ]
            ], [ 'order' => 1 ]);

            return $response->write(
                json_encode($items->toArray())
            );
        }

        public function __invoke($request, $response)
        {
            $params = $request->getParams();
            
            $path = explode('/', trim($request->getUri()->getPath(), '/'));
            
            // Get action method
            $action = array_pop($path);
            
            // Get menu root item id
            $rootId = array_pop($path);

            switch ($action) {
                case 'get':
                    return $response->write(
                        json_encode((new MenuComponent($rootId))->fetch())
                    );
                break;
                
                case 'update':
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
                        json_encode((new MenuComponent($rootId))->fetch())
                    );
                break;
                
                case 'remove':
                    $menu = \Models\Menu::fetchOne([ 
                        'id' => $rootId,
                        'isDeleted' => [ 
                            '$ne' => true 
                        ]
                    ]);

                    if (empty($menu)) {
                        return $response->withStatus(400)->write(
                            json_encode([ 'error' => 'Меню не найдено' ])
                        );
                    }

                    $menu->isDeleted = true;
                    $menu->save();

                    return $response->write(
                        json_encode([ 'success' => true ])
                    );
                break;
            }
        }

        public function itemAdd($request, $response, $args)
        {
            $params = $request->getParams();

            if (empty($params['title']) || empty($params['parrentId'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
                    ])
                );
            }
            
            $item = new \Models\Menu();
            $item->title = $params['title'];
            $item->parrentId = $params['parrentId'];
            $item->link = $params['link'];
            $item->isDeleted = false;
            $item->isHidden = false;
            $item->dateCreated = time();
            $item->save();

            return $response->write(
                json_encode($item->toArray())
            );
        }

        public function itemUpdate($request, $response, $args)
        {
            $params = $request->getParams();

            if (empty($params['title']) || empty($params['parrentId'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено одно из обязательных полей'
                    ])
                );
            }
            
            $item = \Models\Menu::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);
            
            if (empty($item)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Пункт меню не найден'
                    ])
                );
            }
            
            $item->title = $params['title'];
            $item->parrentId = $params['parrentId'];
            $item->link = $params['link'];
            $item->save();

            return $response->write(
                json_encode($item->toArray())
            );
        }

        public function itemRemove($request, $response, $args)
        {
            $item = \Models\Menu::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($item)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Меню не найдено'
                    ])
                );
            }

            $item->isDeleted = true;
            $item->save();

            return $response->write(
                json_encode([
                    'success' => true
                ])
            );
        }
    }
