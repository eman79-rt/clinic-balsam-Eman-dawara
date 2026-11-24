<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['disk','path','collection'];
    public function mediable() { return $this->morphTo(); }
}
