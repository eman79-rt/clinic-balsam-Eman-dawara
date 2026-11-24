<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name','code','city'];
    public function appointments() { return $this->hasMany(Appointment::class); }
    public function doctors() { return $this->belongsToMany(Doctor::class, 'doctor_branch_schedules')->withPivot(['day_of_week','starts_at','ends_at']); }
    public function media() { return $this->morphMany(Media::class, 'mediable'); }
}
