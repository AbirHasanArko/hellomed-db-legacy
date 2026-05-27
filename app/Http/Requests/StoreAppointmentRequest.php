<?php

namespace App\Http\Requests;

use App\Models\Doctor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_email' => ['required', 'email', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:30'],
            'service_mode' => ['required', 'in:online,offline'],
            'scheduled_for' => ['required', 'date', 'after:now'],
            'reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $doctor = Doctor::query()->find($this->integer('doctor_id'));

            if (! $doctor) {
                return;
            }

            if ($this->input('service_mode') === 'online' && ! $doctor->online_available) {
                $validator->errors()->add('service_mode', 'The selected doctor does not offer online consultations.');
            }

            if ($this->input('service_mode') === 'offline' && ! $doctor->offline_available) {
                $validator->errors()->add('service_mode', 'The selected doctor does not offer offline consultations.');
            }
        });
    }
}
