<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id'=>$this->id,'reference'=>$this->reference,'status'=>$this->status->value,'starts_at'=>$this->starts_at?->toISOString(),'ends_at'=>$this->ends_at?->toISOString(),'branch'=>['id'=>$this->branch?->id,'name'=>$this->branch?->name],'doctor'=>['id'=>$this->doctor?->id,'name'=>$this->doctor?->name,'specialty'=>$this->doctor?->specialty?->name],'patient'=>['id'=>$this->patient?->id,'name'=>$this->patient?->name]];
    }
}
