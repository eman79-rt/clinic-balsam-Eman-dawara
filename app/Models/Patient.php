<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['name','phone','last_visit_at','online_booking_blocked_until'];
    protected function casts(): array { return ['last_visit_at' => 'datetime', 'online_booking_blocked_until' => 'datetime']; }
    public function appointments() { return $this->hasMany(Appointment::class); }
    public function media() { return $this->morphMany(Media::class, 'mediable'); }
    public function scopeCanBookOnline($q) { return $q->where(fn ($x) => $x->whereNull('online_booking_blocked_until')->orWhere('online_booking_blocked_until', '<=', now())); }
}
