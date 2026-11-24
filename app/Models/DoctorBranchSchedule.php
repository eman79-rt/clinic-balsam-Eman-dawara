<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorBranchSchedule extends Model
{
    public $timestamps = false;
    protected $fillable = ['doctor_id','branch_id','day_of_week','starts_at','ends_at'];
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
