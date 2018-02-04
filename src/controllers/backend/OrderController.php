<?php
    namespace Controllers\Backend;

    class OrderController
    {
        private static $ORDER_NOT_FOUND_MSG = 'Заказ не найден';
        private static $REQIURED_FIELD_NOT_SET_MSG = 'Не заполнено одно из обязательных полей';

        public function index($request, $response)
        {
            $result = [];

            $orders = \Models\Order::fetchAll([
              'isDeleted' => [ '$ne' => true ]
            ]);

            foreach ($orders as $order) {
              $result[] = $order->expand()->toArray();
            }

            return $response->write(
                json_encode($result)
            );
        }

        public function get($request, $response, $args)
        {
            $order = \Models\Order::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($order)) {
                return $response->withStatus(404)->write(
                    json_encode([ 'error' => self::$ORDER_NOT_FOUND_MSG ])
                );
            }

            return $response->write(
                json_encode($order->expand()->toArray())
            );
        }
        
        private static function validate($data) 
        {
            if (empty($data['products'])) {
                throw new \Exception('Не выбраны товары');
            }
            
            if (empty($data['userName'])) {
                throw new \Exception('Не заполнено имя покупателя');
            }
            
            if (empty($data['address'])) {
                throw new \Exception('Не заполнен адрес');
            }
            
            if (empty($data['phone'])) {
                throw new \Exception('Не заполнен телефон');
            }
            
            if (empty($data['deliveryId'])) {
                throw new \Exception('Не выбран способ доставки');
            }
            
            if (empty($data['paymentId'])) {
                throw new \Exception('Не выбран способ оплаты');
            }
            
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Формат email-адреса неверный');
            }
            
            if (empty($data['stateId'])) {
                throw new \Exception('Не указано текущее состояние заказа');
            }
        }

        public function add($request, $response)
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

            $order = new \Models\Order();
            $order->products = $params['products'];
            $order->stateId = $params['stateId'];
            $order->userName = $params['userName'];
            $order->address = $params['address'];
            $order->email = $params['email'];
            $order->deliveryId = $params['deliveryId'];
            $order->paymentId = $params['paymentId'];
            $order->comment = $params['comment'];
            $order->phone = $params['phone'];
            $order->dateCreated = time();
            $order->isDeleted = false;
            $order->save();

            return $response->write(
                json_encode($order->toArray())
            );
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

            $order = \Models\Order::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($order)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => self::$ORDER_NOT_FOUND_MSG ])
                );
            }

            $order->products = $params['products'];
            $order->stateId = $params['stateId'];
            $order->userName = $params['userName'];
            $order->address = $params['address'];
            $order->email = $params['email'];
            $order->deliveryId = $params['deliveryId'];
            $order->paymentId = $params['paymentId'];
            $order->comment = $params['comment'];
            $order->phone = $params['phone'];
            $order->save();

            return $response->write(
                json_encode($order->toArray())
            );
        }

        public function remove($request, $response, $args)
        {
            $order = \Models\Order::fetchOne([
                'id' => $args['id'],
                'isDeleted' => [
                    '$ne' => true
                ]
            ]);

            if (empty($order)) {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => self::$ORDER_NOT_FOUND_MSG ])
                );
            }

            $order->isDeleted = true;
            $order->save();

            return $response->write(
                json_encode([ 'success' => true ])
            );
        }

    }
