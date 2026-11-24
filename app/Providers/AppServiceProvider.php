<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Observers\AppointmentObserver;
use App\Observers\MedicalRecordObserver;
use App\Services\LogSmsProvider;
use App\Services\SmsProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsProvider::class, LogSmsProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Appointment::observe(AppointmentObserver::class);
        MedicalRecord::observe(MedicalRecordObserver::class);
        Relation::enforceMorphMap([
            'medical_record' => MedicalRecord::class,
            'patient' => \App\Models\Patient::class,
            'doctor' => \App\Models\Doctor::class,
            'branch' => \App\Models\Branch::class,
        ]);
    }
}
