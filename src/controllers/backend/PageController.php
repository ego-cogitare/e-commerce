<?php
    namespace Controllers;

    class PageController
    {
        public function index($request, $response)
        {
            $pages = \Models\Page::fetchAll([ 'isDeleted' => [ '$ne' => true ] ])
                ->toArray();
            
            return $response->withStatus(200)->write(
                json_encode($pages)
            );
        }
        
        public function get($request, $response, $args) 
        {
            $page = \Models\Page::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);
            
            if (empty($page)) {
                return $response->withStatus(404)->write(
                    json_encode([ 'error' => 'Страница не найдена' ])
                );
            }
            
            return $response->write(
                json_encode($page->toArray())
            );
        }
        
        public function add($request, $response) 
        {
            $params = $request->getParams();
            
            if (empty($params['title']) && empty($params['body'])) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Не заполнено одно из обязательных полей' ])
                );
            }
            
            $page = new \Models\Page();
            $page->title = $params['title'];
            $page->body = $params['body'];
            $page->isVisible = filter_var($params['isVisible'], FILTER_VALIDATE_BOOLEAN);
            $page->dateCreated = time();
            $page->isDeleted = false;
            $page->save();
            
            return $response->write(
                json_encode($page->toArray())
            );
        }
        
        public function update($request, $response, $args) 
        {
            $params = $request->getParams();
            
            if (empty($params['title']) || empty($params['body'])) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Не заполнено одно из обязательных полей' ])
                );
            }
            
            $page = \Models\Page::fetchOne([ 
                'id' => $args['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);
            
            if (empty($page)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Страница не найдена' ])
                );
            }
            
            $page->title = $params['title'];
            $page->body = $params['body'];
            $page->isVisible = filter_var($params['isVisible'], FILTER_VALIDATE_BOOLEAN);
            $page->save();
            
            return $response->write(
                json_encode($page->toArray())
            );
        }
        
        public function remove($request, $response, $args) 
        {
            $page = \Models\Page::fetchOne([ 
                'id' => $args['id'],
                'isDeleted' => [ 
                    '$ne' => true 
                ]
            ]);
            
            if (empty($page)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Страница не найдена' ])
                );
            }
            
            $page->isDeleted = true;
            $page->save();
            
            return $response->write(
                json_encode([ 'success' => true ])
            );
        }
        
    }