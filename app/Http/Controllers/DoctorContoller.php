<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\PatientSymptom;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class DoctorContoller
{
    function showPatient()
    {
        $appointments = Appointment::with(['timeSlot', 'doctor', 'patient.patientSymptoms'])
            ->withoutTrashed()
            ->where('status', 'Pending')
            ->get();

        // Hiding unwanted fields in the appointments data
        $appointments->makeHidden(['created_at', 'updated_at', 'deleted_at', 'doctor_id', 'time_id']);

        $appointments->each(function ($appointment) {
            // Hide unnecessary doctor information
            $appointment->doctor->makeHidden(['created_at', 'updated_at', 'deleted_at', 'clinic_id', 'id', 'specialty', 'experience_years', 'email', 'username']);

            // Hide unnecessary timeSlot information
            $appointment->timeSlot->makeHidden(['id', 'duration', 'end_time']);

            // Hide unnecessary patient information
            $appointment->patient->makeHidden(['created_at', 'updated_at', 'deleted_at', 'city_id', 'district_id', 'email', 'username', 'date_of_birth', 'id_number']);

            // Filter to only include the last symptom entry for each patient
            if ($appointment->patient->patientSymptoms->isNotEmpty()) {
                $lastSymptom = $appointment->patient->patientSymptoms->last();
                $appointment->patient->setRelation('patientSymptoms', collect([$lastSymptom]));
            }

            // Hide unnecessary fields in the symptom details
            $appointment->patient->patientSymptoms->each(function ($symptom) {
                $symptom->makeHidden(['created_at', 'updated_at', 'deleted_at', 'user_id', 'id']);
            });
        });

//        return response()->json($appointments);
        return view('doctor_appoint', ['appointments' => $appointments]);

    }

    }

