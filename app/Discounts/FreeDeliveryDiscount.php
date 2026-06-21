<?php

namespace App\Discounts;

use App\Contracts\DiscountInterface;
use App\Models\Discount;
use App\Models\Order;

class FreeDeliveryDiscount implements DiscountInterface
{
    public function calculate(Order $order, Discount $discount): float
    {
        return (float) $order->delivery_fee;
    }
}