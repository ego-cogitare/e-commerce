<?php
    namespace Controllers\Store;

    class CallbackController
    {
        public function index($request, $response)
        {
            $data = $request->getParams();
            
            if (empty($data['userName'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнено имя.',
                        'field' => 'userName'
                    ])
                );
            }
            
            if (empty($data['userPhone'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Не заполнен номер телефона.',
                        'field' => 'userPhone'
                    ])
                );
            }
            
            if (!preg_match('/^0[1-9]\d{8}$/', $data['userPhone'])) {
                return $response->withStatus(400)->write(
                    json_encode([
                        'error' => 'Номер телефона задан неверно.',
                        'field' => 'userPhone'
                    ])
                );
            }
            
            $order = new \Models\Callback();
            $order->name = $data['userName'];
            $order->phone = $data['userPhone'];
            $order->dateCreated = time();
            $order->isDeleted = false;
            $order->save();
            
            return $response->write(
                json_encode([
                    'success' => true, 
                    'message' => 'Спасибо за Вашу заявку. Ожидайте звонка.'
                ])
            );
        }
    }
