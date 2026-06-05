<?php

namespace App\Discounts;

use App\Contracts\DiscountInterface;
use App\Models\Discount;
use App\Models\Order;

class PercentageDiscount implements DiscountInterface
{
    public function calculate(Order $order, Discount $discount): float
    {
        $discountTotal = $order->subtotal * ($discount->value / 100);
        
        if ($discount->max_discount_amount !== null) {
            $discountTotal = min($discountTotal, $discount->max_discount_amount);
        }
        
        return (float) $discountTotal;
    }
}