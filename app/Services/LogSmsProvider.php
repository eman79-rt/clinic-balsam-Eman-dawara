<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LogSmsProvider implements SmsProvider
{
    public function send(string $to, string $message): void { Log::info('SMS simulated', ['to'=>$to,'message'=>$message]); }
}
