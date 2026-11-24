<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = ['name','specialty_id','phone'];
    public function specialty() { return $this->belongsTo(Specialty::class); }
    public function schedules() { return $this->hasMany(DoctorBranchSchedule::class); }
    public function branches() { return $this->belongsToMany(Branch::class, 'doctor_branch_schedules')->withPivot(['day_of_week','starts_at','ends_at']); }
    public function appointments() { return $this->hasMany(Appointment::class); }
    public function media() { return $this->morphMany(Media::class, 'mediable'); }
}
