<?php

namespace App\Discounts;

use App\Contracts\DiscountInterface;
use App\Models\Discount;
use App\Models\Order;

class FirstPurchaseDiscount implements DiscountInterface
{
    public function calculate(Order $order, Discount $discount): float
    {
        $previousOrders = Order::where('customer_id', $order->customer_id)
            ->whereIn('status', ['delivered', 'paid', 'accepted', 'preparing', 'ready', 'picked_up'])
            ->where('id', '!=', $order->id)
            ->count();

        if ($previousOrders > 0) {
            return 0.0;
        }

        $discountTotal = $order->subtotal * ($discount->value / 100);
        if ($discount->max_discount_amount !== null) {
            $discountTotal = min($discountTotal, $discount->max_discount_amount);
        }
        return (float) $discountTotal;
    }
}