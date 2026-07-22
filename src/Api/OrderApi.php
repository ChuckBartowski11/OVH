<?php

declare(strict_types=1);

namespace ChuckBartowski\OvhSdk\Api;

use ChuckBartowski\OvhSdk\Response\ApiResponse;

final class OrderApi extends AbstractApi
{
    public function carts(array $query = []): ApiResponse
    {
        return $this->get('/order/cart', $query);
    }

    public function createCart(string $ovhSubsidiary, array $options = []): ApiResponse
    {
        return $this->post('/order/cart', array_merge($options, ['ovhSubsidiary' => $ovhSubsidiary]));
    }

    public function cart(string $cartId): ApiResponse
    {
        return $this->get('/order/cart/'.$this->encode($cartId));
    }

    public function assignCart(string $cartId): ApiResponse
    {
        return $this->post(sprintf('/order/cart/%s/assign', $this->encode($cartId)));
    }

    public function addItem(string $cartId, string $productType, array $options): ApiResponse
    {
        return $this->post(sprintf('/order/cart/%s/%s', $this->encode($cartId), $this->encode($productType)), $options);
    }

    public function checkout(string $cartId, array $options = []): ApiResponse
    {
        return $this->post(sprintf('/order/cart/%s/checkout', $this->encode($cartId)), $options);
    }

    public function summary(string $cartId): ApiResponse
    {
        return $this->get(sprintf('/order/cart/%s/summary', $this->encode($cartId)));
    }

    public function catalog(string $productName, string $ovhSubsidiary): ApiResponse
    {
        return $this->get(sprintf('/order/catalog/public/%s', $this->encode($productName)), ['ovhSubsidiary' => $ovhSubsidiary]);
    }
}
