<?php

namespace App\Processors;

use App\Models\Order;
use App\Models\PaymentInCash;
use App\Models\PaymentProvider;

class CashOrderProcessor extends OrderProcessor
{
    protected function processPayment(Order $order, float $total): void
    {
        PaymentInCash::create([
            'order_id'    => $order->id,
            'provider_id' => PaymentProvider::where('enabled', true)->orderBy('priority')->first()->id,
            'amount'      => $total,
            'currency'    => 'USD',
            'status'      => 'completed',
            'processed_at' => now(),
        ]);
    }
}