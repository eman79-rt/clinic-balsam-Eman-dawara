<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray($request): array { return ['id'=>$this->id,'name'=>$this->name,'phone'=>$request->user()?->role === 'admin' ? $this->phone : null,'last_visit_at'=>$this->last_visit_at]; }
}
