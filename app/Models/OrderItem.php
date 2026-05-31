<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'item_type', 'item_id', 'quantity', 'unit_price', 'subtotal'];

    public function order() { return $this->belongsTo(Order::class); }
    public function purchasable_item(): MorphTo
    {
        return $this->morphTo('purchasable_item', 'item_type', 'item_id');
    }

    public function getUnitPriceAttribute($value)
    {
        if ($value == 0) {
            $item = $this->purchasable_item;
            if ($item instanceof Product) {
                return $item->price;
            } elseif ($item instanceof ProductBundle) {
                return $item->getTotalPrice();
            }
        }
        return $value;
    }
}
