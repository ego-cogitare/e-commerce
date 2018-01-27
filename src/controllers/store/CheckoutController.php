<?php
    namespace Controllers\Store;

    class CheckoutController
    {
        public function index($request, $response)
        {
            $data = $request->getParams();
            
            if (empty($data['deliveryId'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не указан способ доставки.',
                        'field' => 'deliveryId'
                    ])
                );
            }
            
            /**
             * Validate delivery method
             */
            $delivery = \Models\Settings::fetchOne([
                'key' => 'delivery'
            ])->toArray();
            
            $found = false;
            foreach (json_decode($delivery['value']) as $item) {
                if (intval($data['deliveryId']) === intval($item->id)) {
                    $found = true;
                }
            }
            
            if (!$found) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Указан несуществующий способ доставки.',
                        'field' => 'deliveryId'
                    ])
                );
            }            
            
            if (empty($data['paymentId'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не указан способ оплаты.',
                        'field' => 'paymentId'
                    ])
                );
            }
            
            /**
             * Validate payment method
             */
            $payment = \Models\Settings::fetchOne([
                'key' => 'payment'
            ])->toArray();
            
            $found = false;
            foreach (json_decode($payment['value']) as $item) {
                if (intval($data['paymentId']) === intval($item->id)) {
                    $found = true;
                }
            }
            
            if (!$found) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Указан несуществующий способ оплаты.',
                        'field' => 'deliveryId'
                    ])
                );
            }
            
            if (empty($data['userName'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено имя.',
                        'field' => 'userName'
                    ])
                );
            }
            
            if (empty($data['email'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнен e-mail.',
                        'field' => 'email'
                    ])
                );
            }
            elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Формат e-mail неверный.',
                        'field' => 'email'
                    ])
                );
            }
            
            if (empty($data['phone'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнен номер телефона.',
                        'field' => 'phone'
                    ])
                );
            }
            
            if (empty($data['address'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнен адрес.',
                        'field' => 'address'
                    ])
                );
            }
            
            return $response->write(
                json_encode(['success' => true])
            );
        }
    }
