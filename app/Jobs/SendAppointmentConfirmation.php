<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\SmsProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 3;
    public function __construct(public int $appointmentId) { $this->onQueue('critical'); }
    public function handle(SmsProvider $sms): void
    {
        $appointment = Appointment::with('patient')->findOrFail($this->appointmentId);
        $sms->send($appointment->patient->phone, "تم حجز موعدك. الرقم المرجعي: {$appointment->reference}");
    }
}
