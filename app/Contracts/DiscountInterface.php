<?php

namespace App\Contracts;

use App\Models\Discount;
use App\Models\Order;

interface DiscountInterface
{
    public function calculate(Order $order, Discount $discount): float;
}