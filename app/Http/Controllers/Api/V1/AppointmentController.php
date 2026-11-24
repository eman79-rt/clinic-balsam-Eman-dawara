<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request) { $q=Appointment::with(['branch','doctor.specialty','patient']); $user=$request->user(); if($user->role==='receptionist') $q->where('branch_id',$user->branch_id); elseif($user->role==='doctor') $q->where('doctor_id',$user->doctor_id); foreach(['branch_id','doctor_id','status'] as $f) if($request->filled($f)) $q->where($f,$request->query($f)); if($request->filled('date')) $q->whereDate('starts_at',$request->query('date')); return AppointmentResource::collection($q->latest('starts_at')->paginate(20)); }
    public function store(StoreAppointmentRequest $request, AppointmentService $service) { $appointment=$service->book($request->validated()); return (new AppointmentResource($appointment))->response()->setStatusCode(201); }
}
