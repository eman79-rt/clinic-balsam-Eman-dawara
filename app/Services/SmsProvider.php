<?php

namespace App\Services;

interface SmsProvider
{
    public function send(string $to, string $message): void;
}
