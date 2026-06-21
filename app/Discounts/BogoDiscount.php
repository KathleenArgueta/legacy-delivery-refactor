<?php

namespace App\Discounts;

use App\Contracts\DiscountInterface;
use App\Models\Discount;
use App\Models\Order;

class BogoDiscount implements DiscountInterface
{
    public function calculate(Order $order, Discount $discount): float
    {
        $cheapestPrice = $order->items
            ->map(fn($item) => $item->unit_price)
            ->sort()
            ->values()
            ->first();
            
        return (float) ($cheapestPrice ?? 0.0);
    }
}