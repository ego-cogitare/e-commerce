<?php
    namespace Controllers\Store;
    
    use Components\MenuComponent;

    class BootstrapController
    {
        public function index($request, $response)
        {
            $data = [];
            
            // Extract all menus
            $menus = \Models\Menu::fetchAll([
                '$and' => [
                    [ 'isDeleted' => ['$ne' => true] ],
                    ['$or' => [
                        ['parrentId' => '' ],
                        ['parrentId' => ['$exists' => false] ]
                    ]]
                ]
            ], [ 'order' => 1 ]);
            
            if (count($menus) !== 0) {
                foreach ($menus->toArray() as $menu) {
                    $data['menus'][$menu['id']] = (new MenuComponent($menu['id']))->fetch();
                }
            }
            
            return $response->write(
                json_encode($data)
            );
        }
    }
