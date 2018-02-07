<?php
    namespace Controllers\Store;

    class PaymentController
    {
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
        
        public function form($request, $response) 
        {
            global $app;
            
            $orderId = $request->getParam('orderId');
            
            if (empty($orderId)) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Идентификатор заказа не передан'
                    ])
                );
            }
            
            // Get order information
//            $orderInfo = \Models\Order::fetchOne([ 
//                'id' => $orderId,
//                'isDeleted' => [
//                    '$ne' => true
//                ]
//            ]);
//            
//            if (empty($orderInfo)) {
//                return $response->withStatus(404)->write(
//                    json_encode([
//                        'error' => 'Заявка не найдена'
//                    ])
//                );
//            }
            
            $settings = $app->getContainer()->settings;
            
            $liqpay = new \LiqPay(
                $settings['liqpay']['publicKey'], 
                $settings['liqpay']['privateKey']
            );
            
            $form = $liqpay->cnb_form([
                'action'         => 'pay',
                'amount'         => '1',
                'currency'       => 'UAH',
                'description'    => 'description text',
                'order_id'       => $orderId,
                'version'        => '3',
                'sandbox'        => $settings['liqpay']['sandbox']
            ]);
            
            return $response->write(
                json_encode(['success' => true, 'form' => $form])
            );
        }
    }
