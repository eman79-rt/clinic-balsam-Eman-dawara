<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Models\Appointment;
use App\Models\DoctorBranchSchedule;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function book(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $patient = Patient::whereKey($data['patient_id'])->lockForUpdate()->firstOrFail();
            if ($patient->online_booking_blocked_until?->isFuture()) throw ValidationException::withMessages(['patient_id' => 'المريض ممنوع مؤقتًا من الحجز الإلكتروني.']);
            $starts = \Illuminate\Support\Carbon::parse($data['starts_at']);
            $day = (int) $starts->dayOfWeekIso;
            $onDuty = DoctorBranchSchedule::where('doctor_id', $data['doctor_id'])->where('branch_id', $data['branch_id'])->where('day_of_week', $day)->exists();
            if (!$onDuty) throw ValidationException::withMessages(['doctor_id' => 'الطبيب لا يداوم في هذا الفرع في اليوم المحدد.']);
            $collision = Appointment::where('doctor_id',$data['doctor_id'])->where('branch_id',$data['branch_id'])->where('starts_at',$starts)->whereIn('status',[AppointmentStatus::Booked->value,AppointmentStatus::Arrived->value])->lockForUpdate()->exists();
            if ($collision) throw ValidationException::withMessages(['starts_at' => 'هذا الوقت محجوز مسبقًا.']);
            $data['starts_at'] = $starts;
            $data['ends_at'] = $starts->copy()->addMinutes(30);
            $appointment = Appointment::create($data);
            AppointmentBooked::dispatch($appointment->id);
            return $appointment->load(['branch','doctor.specialty','patient']);
        });
    }

    public function cancel(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) { $appointment->refresh(); $appointment->status = AppointmentStatus::Cancelled; $appointment->save(); AppointmentCancelled::dispatch($appointment->id); return $appointment; });
    }
}
