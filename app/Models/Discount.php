<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'type', 'value', 'min_order_amount', 'max_discount_amount',
                           'valid_from', 'valid_to', 'max_uses', 'current_uses', 'vendor_id'];

    protected $casts = ['valid_from' => 'datetime', 'valid_to' => 'datetime'];

    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function orders() { return $this->belongsToMany(Order::class, 'order_discounts'); }

    public function apply(Order $order): float
    {
        if (now() < $this->valid_from || now() > $this->valid_to) {
            return 0.0;
        }

        if ($this->max_uses !== null && $this->current_uses >= $this->max_uses) {
            return 0.0;
        }

        if ($this->min_order_amount !== null && $order->subtotal < $this->min_order_amount) {
            return 0.0;
        }

        if ($this->vendor_id !== null && $this->vendor_id !== $order->vendor_id) {
            return 0.0;
        }
        $strategies = [
            'percentage'     => \App\Discounts\PercentageDiscount::class,
            'fixed_amount'   => \App\Discounts\FixedAmountDiscount::class,
            'bogo'           => \App\Discounts\BogoDiscount::class,
            'first_purchase' => \App\Discounts\FirstPurchaseDiscount::class,
            'free_delivery'  => \App\Discounts\FreeDeliveryDiscount::class,
        ];

        if (!isset($strategies[$this->type])) {
            \App\Support\Logger::getInstance()->log(
                "Unknown discount type '{$this->type}' for discount {$this->id}", 'warning'
            );
            return 0.0;
        }

        $strategyClass = $strategies[$this->type];
        $strategy = new $strategyClass();

        return $strategy->calculate($order, $this);
    }
}