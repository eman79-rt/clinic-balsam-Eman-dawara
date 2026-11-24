<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['branch_id','doctor_id','patient_id','starts_at','ends_at','status'];
    protected function casts(): array { return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'status' => AppointmentStatus::class]; }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function medicalRecord() { return $this->hasOne(MedicalRecord::class); }
    public function scopeForBranch(Builder $q, int $id): Builder { return $q->where('branch_id', $id); }
    public function scopeForDoctor(Builder $q, int $id): Builder { return $q->where('doctor_id', $id); }
    public function scopeOnDate(Builder $q, string $date): Builder { return $q->whereDate('starts_at', $date); }
    public function scopeWithStatus(Builder $q, AppointmentStatus|string $status): Builder { return $q->where('status', $status instanceof AppointmentStatus ? $status->value : $status); }
}
