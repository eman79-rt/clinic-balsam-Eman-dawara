<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use App\Jobs\SendAppointmentConfirmation;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class QueueAppointmentConfirmation implements ShouldHandleEventsAfterCommit
{
    public function handle(AppointmentBooked $event): void { SendAppointmentConfirmation::dispatch($event->appointmentId)->afterCommit(); }
}
