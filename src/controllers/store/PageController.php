<?php
    namespace Controllers\Store;

    class PageController
    {
        public function get($request, $response, $args)
        {
            $page = \Models\Page::fetchOne([
                'slug' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($page)) {
                return $response->withStatus(404)->write(
                    json_encode([
                        'error' => 'Страница не найдена'
                    ])
                );
            }

            return $response->write(
                json_encode($page->toArray())
            );
        }
    }
