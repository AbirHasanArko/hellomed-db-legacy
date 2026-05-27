<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;

class AppointmentController extends Controller
{
    public function create(Doctor $doctor)
    {
        return view('appointments.create', compact('doctor'));
    }

    public function store(StoreAppointmentRequest $request)
    {
        Appointment::create([
            ...$request->validated(),
            'user_id' => $request->user()?->id,
        ]);

        return redirect()
            ->route('home')
            ->with('status', 'Appointment request submitted successfully.');
    }
}
