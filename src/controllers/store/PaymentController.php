<?php
    namespace Controllers\Store;

    class PaymentController
    {
        /**
         * Process payment status change. LiqPay sends notifications
         * to this endpoint
         */
        public function index($request, $response)
        {
            $data = $request->getParams();
            
            $fd = fopen(getcwd() . '/uploads/liqpay.txt', 'a');
            fwrite($fd, print_r($data, true));
            fclose($fd);
            
//            $order = new \Models\Order();
//            $order->products = $data['order'];
//            $order->stateId = 'new';
//            $order->userName = $data['userName'];
//            $order->address = $data['address'];
//            $order->phone = $data['phone'];
//            $order->email = $data['email'];
//            $order->deliveryId = $data['deliveryId'];
//            $order->paymentId = $data['paymentId'];
//            $order->comment = $data['comments'];
//            $order->dateCreated = time();
//            $order->isDeleted = false;
//            $order->save();
            
            return $response->write(
                json_encode(['success' => true])
            );
        }
        
        /**
         * Generate LiqPay form
         */
        public function form($request, $response) 
        {
            global $app;
            
            $orderId = $request->getParam('orderId');
            
            if (empty($orderId)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'success' => false,
                        'error' => 'Идентификатор заказа не передан.'
                    ])
                );
            }
            
            // Looking for order in store
            try {
                $order = \Models\Order::fetchOne([
                    'isDeleted' => [
                        '$ne' => true
                    ],
                    'paymentId' => '1517068714998',
                    'stateId' => 'new',
                    'id' => $orderId
                ]);
            }
            catch (\Exception $e) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'success' => false,
                        'error' => $e->getMessage()
                    ])
                );
            }
            
            if (empty($order)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'success' => false,
                        'error' => 'Заказ не найден.'
                    ])
                );
            }
            
            $settings = $app->getContainer()->settings;
            
            $liqpay = new \LiqPay(
                $settings['liqpay']['publicKey'], 
                $settings['liqpay']['privateKey']
            );
            
            $form = $liqpay->cnb_form([
                'action'         => 'pay',
                'amount'         => sprintf('%.2f', $order->price),
                'currency'       => 'UAH',
                'description'    => 'Оплата товаров JUNIMED',
                'order_id'       => $orderId,
                'version'        => '3',
                'sandbox'        => $settings['liqpay']['sandbox']
            ]);
            
            return $response->write(
                json_encode(['success' => true, 'form' => $form])
            );
        }
    }
