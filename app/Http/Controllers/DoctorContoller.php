<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DoctorContoller extends Controller
{
    public function showPatient()
    {
        $user = Auth::guard('doctor')->user();

        // Fetch pending appointments with related data
        $appointments = Appointment::with(['timeSlot', 'patient.patientSymptoms' => function ($query) {
            $query->latest()->limit(1); // Limit to latest symptom
        }])
            ->where('status', 'Pending')
            ->where('doctor_id', $user->id)
            ->orderBy('appointment_date') // Ensures appointments are sorted by date in the DB
            ->get()
            ->map(function ($item) {
                // Simplify the data structure
                $item['start_time'] = $item->timeSlot->start_time;
                return $item;
            })
            ->makeHidden(["timeSlot", 'created_at', 'updated_at', 'deleted_at', 'doctor_id', 'time_id', 'patient_id', 'id']);

        $appointments->each(function ($appointment) {
            // Hide sensitive patient data
            $appointment->patient->makeHidden(['created_at', 'updated_at', 'deleted_at', 'city_id', 'district_id', 'email', 'username', 'date_of_birth', 'id_number']);
            // Limit to the latest symptom
            $appointment->patient->patientSymptoms->each->makeHidden(['created_at', 'updated_at', 'deleted_at', 'user_id', 'id']);
        });

        // Sort by date and start time
        $sortedAppointments = $appointments->sortBy([
            ['appointment_date', 'asc'],
            ['start_time', 'asc'],
        ])->values();

        // Assign new sequential IDs
        $appointmentsWithNewIds = $sortedAppointments->values()->map(function ($appointment, $index) {
            $appointment['id'] = $index + 1; // New ID
            return $appointment;
        });

//        return response()->json($appointmentsWithNewIds);
//
        return view('doctor_appoint', ['appointments' => $appointmentsWithNewIds]);
    }
}
