<?php

namespace App\Models;

use App\Contracts\NotifiableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Customer extends Model implements NotifiableInterface
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id', 'address', 'city', 'latitude', 'longitude',
        'verified', 'preferred_payment_method', 'loyalty_points'
    ];

    protected $casts = ['verified' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function notifications() { 
        return $this->hasMany(Notification::class, 'recipient_id')
                    ->where('recipient_type', 'customer'); 
    }

    public function sendNotification(string $message): void
    {
        \Log::info("Notificando al cliente {$this->id}: $message");
    }

    public function placeOrder(array $cart, string $paymentMethod): Order
    {
        if ($paymentMethod === 'cash') {
            $processor = new \App\Processors\CashOrderProcessor();
        } else {
            $processor = new \App\Processors\WompiOrderProcessor();
        }

        return $processor->process($this, $cart);
    }
}