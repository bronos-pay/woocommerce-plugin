<?php

namespace BronosPay\Services;

use BronosPay\TestCase;

class OrderServiceTest extends TestCase
{
    /** @var OrderService */
    private $service;

    /**
     * @before
     */
    protected function setUpService()
    {
        $client = $this->createSandboxClient();

        $this->service = new OrderService($client);
    }

    public function testCreateOrder()
    {
        $params = self::getGoodPostParams();

        $order = $this->service->create($params);

        $this->assertNotEmpty($order->_id);
        $this->assertSame($order->reference_id, $params['reference_id']);

        return $order;
    }

    public function testInvalidCreateOrder()
    {
        $this->expectException('BronosPay\Exception\Api\UnprocessableEntity');

        $this->service->create([]);
    }

    /**
     * @depends testCreateOrder
     */
    public function testCancelOrder($order)
    {
        $response = $this->service->cancel($order->_id);
        $this->assertSame($order->status, 'Symbol(created)');
        $this->assertSame($order->_id, $response->_id);
        $this->assertSame($response->status, 'Symbol(cancelled)');
    }

    /**
     * @depends testCreateOrder
     */
    // public function testCheckout($order)
    // {
    //     $response = $this->service->checkout($order->id, [
    //         'pay_currency' => 'BTC'
    //     ]);

    //     $this->assertSame($order->id, $response->id);
    // }

    // public function testInvalidCheckout()
    // {
    //     $this->expectException('BronosPay\Exception\Api\OrderNotFound');

    //     $this->service->checkout(0, [
    //         'pay_currency' => 'BTC'
    //     ]);
    // }

    /**
     * @depends testCreateOrder
     */
    public function testGetOrder($order)
    {
        $response = $this->service->get($order->_id);

        $this->assertSame($order->_id, $response->_id);
    }

    public function testInvalidGetOrder()
    {
        $this->expectException('BronosPay\Exception\Api\UnprocessableEntity');

        $this->service->get("0");
    }

    public function testListOrders()
    {
        $response = $this->service->list([
            'wallet_id' => 'order-id',
            'status' => 'Symbol(completed)',
            'offset' => '0',
            'limit' => '100'
        ]);

        $this->assertNotEmpty($response);
    }

    public static function getGoodPostParams(): array
    {
        return [
            'reference_id'          => 'YOUR-CUSTOM-ORDER-ID-115',
            'amount'      => '1050.99',
            'currency'    => 'USD',
            'receive_currency'  => 'EUR',
            'callback_url'      => 'https://example.com/payments?token=6tCENGUYI62ojkuzDPX7Jg',
            'cancel_url'        => 'https://example.com/cart',
            'success_url'       => 'https://example.com/account/orders',
            'title'             => 'Order #112',
            'description'       => 'Apple Iphone 6'
        ];
    }
}
