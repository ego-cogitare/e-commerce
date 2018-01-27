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

        public function add($request, $response)
        {
            $params = $request->getParams();

            if (empty($params['userName']) || empty($params['address']) ||
                empty($params['phone']) || empty($params['products']) ||
                empty($params['email']) || empty($params['stateId']))
            {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => self::$REQIURED_FIELD_NOT_SET_MSG ])
                );
            }

            $order = new \Models\Order();
            $order->products = $params['products'];
            $order->stateId = $params['stateId'];
            $order->userName = $params['userName'];
            $order->address = $params['address'];
            $order->email = $params['email'];
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

            if (empty($params['userName']) || empty($params['address']) ||
                empty($params['phone']) || empty($params['products']) ||
                empty($params['email']) || empty($params['stateId']))
            {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => self::$REQIURED_FIELD_NOT_SET_MSG ])
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
