<?php

namespace Controllers\Store;


/**
 * Class PaymentController
 * @package Controllers\Store
 */
class PaymentController
{
    /**
     * Process payment status change. LiqPay sends notifications
     * to this endpoint
     * @param $request
     * @param $response
     * @return mixed
     */
    public function index($request, $response)
    {
        global $app;
        
        $settings = $app->getContainer()->settings;
        
        /** @var array $response */
        $response = $request->getParams();
        
        if (empty($response['signature']) || empty($response['data'])) {
            throw new \Exception('Wrong payment information.');
        }
        
        /** @var string|json $data */
        $data = base64_decode($response['data']);
        
        /** @var array $data Response from liqpay */
        $data = json_decode($data, true);
        
        /** Save text logs */
        $log = date('d.m.Y H:i:s') . ':' . PHP_EOL . print_r($data, true) . PHP_EOL . PHP_EOL;
        file_put_contents(getcwd() . '/uploads/liqpay.log', $log, FILE_APPEND);
        
        /** @var string $signature Payment signature */
        $signature = base64_encode(sha1($settings['liqpay']['privateKey'] . $response['data'] . $settings['liqpay']['privateKey'], 1));
        
        if ($response['signature'] !== $signature) {
            throw new \Exception('Wrong signature.');
        }
        
        $order = \Models\Order::fetchOne([
            'id' => $data['order_id'],
            'isDeleted' => [
                '$ne' => true
            ]
        ]);
        
        if (empty($order)) {
            throw new \Exception('Order not found.');
        }
        
        /** If payment is success and order state not set to "payed" yet */
        if (($settings['liqpay']['sandbox'] == 1 && $data['status'] === 'sandbox' || $data['status'] === 'success') && ($order->stateId !== 'payed')) {
            $order->stateId = 'payed';
            $order->save();
            
            /** Save payment information */
            $payment = new \Models\Payment();
            $payment->orderId = $data['order_id'];
            $payment->price = $data['amount'];
            $payment->rawData = json_encode($data, JSON_PRETTY_PRINT);
            $payment->dateCreated = time();
            $payment->save();
        }

//        {
//            "action": "pay",
//            "payment_id": 773185433,
//            "status": "sandbox",
//            "version": 3,
//            "type": "buy",
//            "paytype": "card",
//            "public_key": "i95189456725",
//            "acq_id": 414963,
//            "order_id": "5b635761d57fd568b621a2a2",
//            "liqpay_order_id": "50DPTND11533237124112775",
//            "description": "Оплата товаров JUNIMED",
//            "sender_card_mask2": "516875*08",
//            "sender_card_bank": "pb",
//            "sender_card_type": "mc",
//            "sender_card_country": 804,
//            "ip": "46.149.49.15",
//            "amount": 1.01,
//            "currency": "UAH",
//            "sender_commission": 0,
//            "receiver_commission": 0.03,
//            "agent_commission": 0,
//            "amount_debit": 1.01,
//            "amount_credit": 1.01,
//            "commission_debit": 0,
//            "commission_credit": 0.03,
//            "currency_debit": "UAH",
//            "currency_credit": "UAH",
//            "sender_bonus": 0,
//            "amount_bonus": 0,
//            "mpi_eci": "7",
//            "is_3ds": false,
//            "create_date": 1533237124150,
//            "end_date": 1533237124150,
//            "transaction_id": 773185433
//        }
        
        echo 'ok';
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
            'sandbox'        => $settings['liqpay']['sandbox'],
            'server_url'     => $settings['liqpay']['server_url'],
            'result_url'     => $settings['liqpay']['result_url'],
        ]);

        return $response->write(
            json_encode(['success' => true, 'form' => $form])
        );
    }
}
