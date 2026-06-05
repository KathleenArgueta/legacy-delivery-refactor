<?php

namespace App\Processors;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\Vendor;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;

abstract class OrderProcessor
{
    // El Template Method que define el esqueleto
    public final function process(Customer $customer, array $cart): Order
    {
        if (empty($cart['items'])) {
            throw new \Exception('Cart is empty.');
        }
        if (!$customer->verified) {
            throw new \Exception('Customer account is not verified.');
        }

        $vendor = Vendor::find($cart['vendor_id']);
        if (!$vendor || $vendor->status !== 'active') {
            throw new \Exception('Vendor is not available.');
        }

        $openingHours = $vendor->opening_hours ?? [];
        $now = now();
        $dayKey = strtolower($now->format('l'));
        if (!empty($openingHours[$dayKey]) && ($openingHours[$dayKey]['closed'] ?? false)) {
            throw new \Exception('Vendor is currently closed.');
        }

        $subtotal = 0.0;
        $orderItems = [];

        foreach ($cart['items'] as $item) {
            if ($item['type'] === 'product') {
                $product = Product::find($item['id']);
                if (!$product || !$product->available) {
                    throw new \Exception("Product {$item['id']} is not available.");
                }
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}.");
                }
                $unitPrice = $product->price;
                $orderItems[] = [
                    'item_type' => 'product',
                    'item_id'   => $product->id,
                    'quantity'  => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal'   => $unitPrice * $item['quantity'],
                ];
                $subtotal += $unitPrice * $item['quantity'];

            } elseif ($item['type'] === 'bundle') {
                $bundle = ProductBundle::find($item['id']);
                if (!$bundle || !$bundle->available) {
                    throw new \Exception("Bundle {$item['id']} is not available.");
                }
                $unitPrice = $bundle->getTotalPrice();
                $orderItems[] = [
                    'item_type' => 'bundle',
                    'item_id'   => $bundle->id,
                    'quantity'  => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal'   => $unitPrice * $item['quantity'],
                ];
                $subtotal += $unitPrice * $item['quantity'];
            }
        }

        if ($subtotal < 5.00) {
            throw new \Exception('Minimum order amount is $5.00.');
        }

        $discountTotal = 0.0;
        $appliedDiscount = null;

        if (!empty($cart['discount_code'])) {
            $discount = Discount::where('code', $cart['discount_code'])->first();
            if ($discount && now() >= $discount->valid_from && now() <= $discount->valid_to) {
                if (!$discount->max_uses || $discount->current_uses < $discount->max_uses) {
                    switch ($discount->type) {
                        case 'percentage':
                            $discountTotal = $subtotal * ($discount->value / 100);
                            if ($discount->max_discount_amount) {
                                $discountTotal = min($discountTotal, $discount->max_discount_amount);
                            }
                            break;
                        case 'fixed_amount':
                            $discountTotal = min($discount->value, $subtotal);
                            break;
                        case 'free_delivery':
                            break;
                    }
                    $appliedDiscount = $discount;
                }
            }
        }

        $deliveryFee = 2.50;
        if ($subtotal > 20.00) { $deliveryFee = 1.50; }
        if ($subtotal > 50.00) { $deliveryFee = 0.00; }
        if ($appliedDiscount && $appliedDiscount->type === 'free_delivery') {
            $deliveryFee = 0.00;
        }

        $total = $subtotal - $discountTotal + $deliveryFee;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'customer_id'      => $customer->id,
                'vendor_id'        => $cart['vendor_id'],
                'status'           => 'created',
                'subtotal'         => $subtotal,
                'discount_total'   => $discountTotal,
                'delivery_fee'     => $deliveryFee,
                'total'            => $total,
                'delivery_address' => $customer->address,
                'notes'            => $cart['notes'] ?? null,
            ]);

            foreach ($orderItems as $itemData) {
                OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));
            }

            foreach ($cart['items'] as $item) {
                if ($item['type'] === 'product') {
                    $product = Product::find($item['id']);
                    $product->stock -= $item['quantity'];
                    if ($product->stock <= 0) {
                        $product->available = false;
                    }
                    $product->save();
                }
            }

            if ($appliedDiscount) {
                $appliedDiscount->current_uses++;
                $appliedDiscount->save();
                $order->discounts()->attach($appliedDiscount->id);
            }

            $this->processPayment($order, $total);

            $order->status = 'paid';
            $order->save();

            $emailService = new \App\Services\EmailService();
            $smsService   = new \App\Services\SMSService();

            $emailService->send(
                $customer->user->email,
                'Pedido confirmado',
                "Tu pedido #{$order->id} ha sido recibido. Total: \${$total}"
            );
            $emailService->send(
                $vendor->user->email,
                'Nuevo pedido',
                "Tienes un nuevo pedido #{$order->id} por \${$total}"
            );
            if ($customer->user->phone) {
                $smsService->send(
                    $customer->user->phone,
                    "Pedido #{$order->id} confirmado. Total: \${$total}"
                );
            }

            \App\Support\Logger::getInstance()->log(
                "Order {$order->id} created by customer {$customer->id}. Total: {$total}"
            );

            $customer->loyalty_points += (int) floor($total);
            $customer->save();

            DB::commit();
            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            \App\Support\Logger::getInstance()->log(
                "Order creation failed for customer {$customer->id}: " . $e->getMessage(), 'error'
            );
            throw $e;
        }
    }

    // Este es el paso que cada subclase tiene que implementar
    abstract protected function processPayment(Order $order, float $total): void;
}