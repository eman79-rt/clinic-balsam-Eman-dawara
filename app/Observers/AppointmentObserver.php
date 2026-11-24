<?php

namespace App\Observers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AppointmentObserver
{
    public function creating(Appointment $appointment): void
    {
        if ($appointment->reference) return;
        $year = $appointment->starts_at?->year ?? now()->year;
        $branch = \App\Models\Branch::findOrFail($appointment->branch_id);
        $key = ['branch_id' => $branch->id, 'year' => $year];
        $row = DB::table('branch_year_sequences')->where($key)->lockForUpdate()->first();
        if (!$row) {
            DB::table('branch_year_sequences')->insertOrIgnore([...$key, 'last_value' => 0]);
            $row = DB::table('branch_year_sequences')->where($key)->lockForUpdate()->first();
        }
        $next = $row->last_value + 1;
        DB::table('branch_year_sequences')->where('id', $row->id)->update(['last_value' => $next]);
        $appointment->reference = sprintf('%s-%d-%06d', $branch->code, $year, $next);
    }

    public function created(Appointment $appointment): void { $this->forget($appointment); }
    public function updated(Appointment $appointment): void { if ($appointment->wasChanged(['branch_id','doctor_id','starts_at','status'])) $this->forget($appointment); }
    public function deleted(Appointment $appointment): void { $this->forget($appointment); }
    private function forget(Appointment $appointment): void
    {
        $day = $appointment->starts_at->toDateString();
        Cache::store('redis')->forget("availability:{$appointment->branch_id}:{$appointment->doctor_id}:{$day}");
    }
}
