<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array { return ['branch_id'=>['required','integer','exists:branches,id'],'doctor_id'=>['required','integer','exists:doctors,id'],'patient_id'=>['required','integer','exists:patients,id'],'starts_at'=>['required','date','after:now']]; }
    public function messages(): array { return ['branch_id.required'=>'الفرع مطلوب.','doctor_id.required'=>'الطبيب مطلوب.','patient_id.required'=>'المريض مطلوب.','starts_at.required'=>'وقت الموعد مطلوب.','starts_at.after'=>'يجب أن يكون الموعد في المستقبل.']; }
}
