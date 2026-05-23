<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
{
    $this->app->bind(NotificationChannelInterface::class, function ($app, $params) {
        return match ($params['channel']) {
            'email' => new \App\Services\EmailService(),
            'sms'   => new \App\Services\SMSService(),
            default => throw new \Exception("Canal no soportado"),
        };
    });
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    Relation::morphMap([
        'product' => \App\Models\Product::class,
        'bundle'  => \App\Models\ProductBundle::class,
    ]);
}
}
