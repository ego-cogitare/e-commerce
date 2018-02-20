<?php
    namespace Controllers\Backend;

    class CallbackController
    {
        public function index($request, $response)
        {
            $result = [];

            $callbacks = \Models\Callback::fetchAll([
              'isDeleted' => [ '$ne' => true ]
            ]);

            foreach ($callbacks as $callback) {
              $result[] = $callback->toArray();
            }

            return $response->write(
                json_encode($result)
            );
        }

        public function get($request, $response, $args)
        {
            $callback = \Models\Callback::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($callback)) {
                return $response->withStatus(404)->write(
                    json_encode([ 'error' => 'Заявка не найдена' ])
                );
            }

            return $response->write(
                json_encode($callback->toArray())
            );
        }
        
        private static function validate($data) 
        {
            if (empty($data['name'])) {
                throw new \Exception('Не заполнено имя покупателя');
            }

            if (empty($data['phone'])) {
                throw new \Exception('Не заполнен телефон');
            }
        }

        public function update($request, $response, $args)
        {
            $params = $request->getParams();

            try {
                self::validate($params, $response);
            }
            catch (\Exception $e) {
                return $response->withStatus(400)->write(
                    json_encode(['error' => $e->getMessage()])
                );
            }

            $callback = \Models\Callback::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($callback)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Заявка не найдена' ])
                );
            }

            $callback->isProcessed = filter_var($params['isProcessed'], FILTER_VALIDATE_BOOLEAN);
            $callback->name = $params['name'];
            $callback->comment = $params['comment'];
            $callback->phone = $params['phone'];
            $callback->save();

            return $response->write(
                json_encode($callback->toArray())
            );
        }

        public function remove($request, $response, $args)
        {
            $callback = \Models\Callback::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($callback)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => 'Заявка не найдена' ])
                );
            }

            $callback->isDeleted = true;
            $callback->save();

            return $response->write(
                json_encode([ 'success' => true ])
            );
        }

    }
