<?php

namespace App\Contracts;

interface NotifiableInterface
{
    public function sendNotification(string $message): void;
}
