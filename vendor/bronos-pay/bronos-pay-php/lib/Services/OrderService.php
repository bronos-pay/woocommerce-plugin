<?php

namespace BronosPay\Services;

use BronosPay\Resources\CreateOrder;
use BronosPay\Resources\Checkout;
use BronosPay\Resources\Order;

class OrderService extends AbstractService
{
    /**
     * Create order at BronosPay and redirect shopper to invoice (payment_url).
     *
     * @param  string[] $params
     * @return CreateOrder|mixed
     */
    public function create(array $params = [])
    {
        return $this->request('post', '/api/v1/orders/create', $params);
    }


    /**
     * Cancel order at BronosPay and redirect shopper to failed page.
     *
     * @param  string $id
     * @return Order|mixed
     */
    public function cancel(string $id)
    {
        return $this->request('put', $this->buildPath('/api/v1/orders/%s/cancel', $id));
    }

    /**
     * @param  int   $id
     * @param  string[] $params
     * @return Checkout|mixed
     */
    public function checkout(int $id, array $params = [])
    {
        return $this->request('post', $this->buildPath('/api/v1/orders/%s/checkout', $id), $params);
    }

    /**
     * Retrieving information of a specific order by BronosPay order ID.
     *
     * @param  string $id
     * @return Order|mixed
     */
    public function get(string $id)
    {
        return $this->request('get', $this->buildPath('/api/v1/orders/get?id=%s', $id));
    }

    /**
     * Retrieving information of all placed orders.
     *
     * @param  string[] $params
     * @return Order[]|mixed
     */
    public function list(array $params = [])
    {
        return $this->request('get', '/api/v1/orders/list', $params);
    }
}
