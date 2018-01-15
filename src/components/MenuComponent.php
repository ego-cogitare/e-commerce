<?php

namespace Components;

class MenuComponent 
{
    private $menuTree = [];
    private $keyPrefix = 'menu-container::';
    private $rootId = null;
    
    public function __construct($rootId)
    {
        $this->rootId = $rootId;
    }
    
    public function fetch()
    {
        $this->menuTree = [];

        $items = \Models\Menu::fetchAll([
            'isDeleted' => [
                '$ne' => true
            ],
            'id' => $this->rootId
        ], [ 'order' => 1 ]);

        if (count($items) === 0) {
            throw new \Exception('Не найден корневой элемент меню: ' . $this->rootId);
        }

        foreach ($items as $item) {
            $this->_fetchBranch([ 
                $item->toArray()
            ]);
        }

        array_walk_recursive($this->menuTree, function(&$value) {
            if (preg_match("/^{$this->keyPrefix}\w+$/", $value)) {
                $value = [];
            }
        });

        return $this->menuTree;
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
}
