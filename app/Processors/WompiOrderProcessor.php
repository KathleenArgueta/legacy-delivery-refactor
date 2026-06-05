<?php

namespace App\Processors;

use App\Models\Order;
use App\Models\PaymentByCard;
use App\Models\PaymentProvider;
use App\Services\Payments\WompiHandler;

class WompiOrderProcessor extends OrderProcessor
{
    protected function processPayment(Order $order, float $total): void
    {
        $wompiHandler = new WompiHandler();
        $result = $wompiHandler->cobrar($total, 'USD', [
            'referencia'  => "ORDER-{$order->id}",
            'descripcion' => "Pago orden #{$order->id}",
        ]);
        $success = $result['estado'] === 'APROBADO';

        PaymentByCard::create([
            'order_id'                => $order->id,
            'provider_id'             => PaymentProvider::where('name', 'Wompi')->first()->id,
            'amount'                  => $total,
            'currency'                => 'USD',
            'status'                  => $success ? 'completed' : 'failed',
            'external_transaction_id' => $result['id_transaccion'] ?? null,
            'raw_response'            => $result,
            'processed_at'            => $success ? now() : null,
        ]);

        if (!$success) {
            throw new \Exception('Payment processing failed.');
        }
    }
}