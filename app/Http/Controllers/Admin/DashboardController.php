<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Article;
use App\Models\Department;
use App\Models\Doctor;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'doctorCount' => Doctor::query()->count(),
            'departmentCount' => Department::query()->count(),
            'appointmentCount' => Appointment::query()->count(),
            'articleCount' => Article::query()->count(),
        ]);
    }
}
