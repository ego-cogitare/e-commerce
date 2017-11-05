<?php
    namespace Controllers;

    class OrderController
    {
        private static $ORDER_NOT_FOUND_MSG = 'Заказ не найден';
        private static $REQIURED_FIELD_NOT_SET_MSG = 'Не заполнено одно из обязательных полей';

        public function index($request, $response)
        {
            $orders = \Models\Order::fetchAll([ 'isDeleted' => [ '$ne' => true ] ])
                ->toArray();

            return $response->write(
                json_encode($orders)
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

            if (count($order->products) > 0) {
              $order->products = array_map(
                function($item) {
                  $product = \Models\Product::fetchOne([
                      'id' => $item['id'],
                      'isDeleted' => [
                          '$ne' => true
                      ]
                  ]);

                  return array_merge(
                    $product->expand()->toArray(),
                    ['count' => (int)$item['count']]
                  );
                },
                $order->products
              );
            }

            return $response->write(
                json_encode($order->toArray())
            );
        }

        public function add($request, $response)
        {
            $params = $request->getParams();

            if (empty($params['firstName']) || empty($params['lastName']) ||
                empty($params['phone']) || empty($params['products']))
            {
                return $response->withStatus(400)->write(
                    json_encode([ 'error' => self::$REQIURED_FIELD_NOT_SET_MSG ])
                );
            }

            $order = new \Models\Order();
            $order->products = $params['products'];
            $order->firstName = $params['firstName'];
            $order->lastName = $params['lastName'];
            $order->email = $params['email'];
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

            if (empty($params['firstName']) || empty($params['lastName']) ||
                empty($params['phone']) || empty($params['products']))
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
            $order->firstName = $params['firstName'];
            $order->lastName = $params['lastName'];
            $order->email = $params['email'];
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
