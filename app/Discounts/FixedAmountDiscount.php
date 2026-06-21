<?php

namespace App\Discounts;

use App\Contracts\DiscountInterface;
use App\Models\Discount;
use App\Models\Order;

class FixedAmountDiscount implements DiscountInterface
{
    public function calculate(Order $order, Discount $discount): float
    {
        return (float) min($discount->value, $order->subtotal);
    }
}