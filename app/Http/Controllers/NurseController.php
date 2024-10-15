<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class NurseController
{
    function showPatient()
    {
        $appointments = Appointment::with(['timeSlot','doctor','patient'])->withoutTrashed()->where('status','Pending')->get();

        $appointments->makeHidden(['created_at', 'updated_at', 'deleted_at','doctor_id','time_id']);

        $appointments->each(function ($appointment) {
            $appointment->doctor->makeHidden(['created_at', 'updated_at', 'deleted_at', 'clinic_id','id','specialty','experience_years','email','username']);
            $appointment->timeSlot->makeHidden(['id','duration','end_time']);
            $appointment->patient->makeHidden(['created_at', 'updated_at', 'deleted_at','city_id','district_id','email','username','date_of_birth','id_number','id']);
        });
            return view('nurse', ['appointments' => $appointments]);
    }
}


//return response()->json($appointments);
