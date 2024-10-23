<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\District;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\PatientSymptom;
use App\Models\Symptom;
use App\Models\TimeSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Display the booking form or appointment details.
     */
    public function showBookingForm(): View
    {
        $this->checkAppointment();



//
//        } else {
            $symptoms = Symptom::all();

            return view('patient_symptoms', compact('symptoms'));
//        }
    }

    /**
     * Show available time slots for booking.
     */
    public function showTimeChoose(Request $request)
    {
        $this->checkAppointment();

        $user = Auth::guard('web')->user(); // Return the authenticated user
        $doctorId = $request->session()->get('doctor_id');
        $districtId = $user->district_id;

        // Calculate time slot duration based on district population
        $population = District::where('id', $districtId)->value('population');
        $timeSlotDuration = $population < 25000 ? 15 : 10;

        // Get today's date and one month ahead
        $today = Carbon::today();
        $endDate = $today->copy()->addMonth();

        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereNot('status', 'Cancelled')
            ->whereNot('status', 'Done')
            ->whereBetween('appointment_date', [$today, $endDate])
            ->get(['appointment_date', 'time_id']);

        $bookedAppointmentsByDate = $appointments->isEmpty() ? [] : $appointments->groupBy('appointment_date');

//        $bookedAppointmentsByDate = $appointments->groupBy('appointment_date');

        // Define time frames for severity levels
        $severityTimeFrames = [
            3 => ['start' => '8:00:00', 'end' => '12:00:00'],
            2 => ['start' => '12:00:00', 'end' => '15:00:00'],
            1 => ['start' => '15:00:00', 'end' => '17:00:00'],
        ];

        $level = PatientSymptom::withoutTrashed()
            ->where('user_id', $user->id)
            ->pluck('level')
            ->last();

        $startTime = $severityTimeFrames[$level]['start'];
        $endTime = $severityTimeFrames[$level]['end'];

        $timeSlots = TimeSlot::where('duration', $timeSlotDuration)
            ->where('start_time', '>=', $startTime)
            ->where('end_time', '<=', $endTime)
            ->get(['id', 'start_time', 'duration']);

        $doctorAvailability = DoctorSlot::where('doctor_id', $doctorId)
            ->get(['day', 'start_time', 'end_time'])
            ->keyBy('day');

        $availableSlotsByDate = [];

        foreach ($today->daysUntil($endDate) as $date) {
            $dayOfWeek = $date->format('l');

            // Check doctor availability
            if (!$doctorAvailability->has($dayOfWeek)) {
                continue;
            }

            $doctorStartTime = $doctorAvailability[$dayOfWeek]['start_time'];
            $doctorEndTime = $doctorAvailability[$dayOfWeek]['end_time'];
            $appointmentsForDate = $bookedAppointmentsByDate[$date->toDateString()] ?? collect();
            $bookedTimeIds = $appointmentsForDate->pluck('time_id')->toArray();

            // Filter slots based on bookings and doctor availability
            $availableSlots = $timeSlots->filter(function ($slot) use ($bookedTimeIds, $doctorStartTime, $doctorEndTime) {
                return !in_array($slot->id, $bookedTimeIds) &&
                    $slot->start_time >= $doctorStartTime &&
                    $slot->start_time <= $doctorEndTime;
            })->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                ];
            });

            $availableSlotsByDate[$date->toDateString()] = [
                'day' => $dayOfWeek,
                'slots' => $availableSlots->values()->toArray(),
            ];
        }

        ksort($availableSlotsByDate);

//        return response()->json($availableSlotsByDate);
//
        return view('times', ['availableTimes' => $availableSlotsByDate]);
    }

    /**
     * Book an appointment.
     */
    public function bookSlot(Request $request): RedirectResponse
    {
        $this->checkAppointment();
        $validatedData = $request->validate([
            'time_slot' => 'required|integer|exists:time_slots,id',
            'appointment_date' => 'required|date|after_or_equal:today',
        ], [
            'time_slot.required' => 'You must select a time slot.',
            'time_slot.exists' => 'The selected time slot does not exist.',
            'appointment_date.required' => 'The appointment date is required.',
            'appointment_date.after_or_equal' => 'The appointment date must be today or in the future.',
        ]);

        $user = Auth::user();
        $timeSlot = TimeSlot::findOrFail($validatedData['time_slot']);
        $appointmentDate = Carbon::parse($validatedData['appointment_date']);
        $doctorId = $request->session()->get('doctor_id');

        Appointment::create([
            'doctor_id' => $doctorId,
            'patient_id' => $user->id,
            'appointment_date' => $appointmentDate,
            'time_id' => $timeSlot->id,
            'status' => 'Pending',
        ]);

        return redirect()->route('success')->with('success', 'Your appointment has been successfully booked!');
    }

    /**
     * Store patient symptoms.
     */
    public function storePatientSymptoms(Request $request)
    {
        $this->checkAppointment();


        $validatedData = $request->validate([
            'symptoms_list' => 'required|json',
        ], [
            'symptoms_list.required' => 'Please select at least one symptom.',
            'symptoms_list.json' => 'Invalid symptoms format. Please try again.',
        ]);

        if (!Auth::check()) {
            return redirect()->back()->withErrors(['auth' => 'You must be logged in to book an appointment.']);
        }

        $userId = Auth::id();
        $symptoms = json_decode($validatedData['symptoms_list'], true);

        if (!is_array($symptoms)) {
            return redirect()->back()->withErrors(['symptoms' => 'Invalid symptoms data provided. Please try again.']);
        }

        $isEmergency = Symptom::whereIn('name', $symptoms)->where('is_emergency', true)->exists();
        $level = $isEmergency ? 3 : (count($symptoms) > 1 ? 2 : 1);

        PatientSymptom::create([
            'user_id' => $userId,
            'symptoms' => json_encode($symptoms),
            'level' => $level,
        ]);

        return $this->showTimeChoose($request);
    }

    /**
     * Update appointment data.
     */
    public function updateData(): void
    {
        $now = Carbon::now();
        $todayDate = $now->toDateString();
        $currentTime = $now->toTimeString();

        Appointment::where('appointment_date', '<', $todayDate)
            ->where('status', '!=', 'done')
            ->delete();

        $todaysAppointments = Appointment::where('appointment_date', '=', $todayDate)
            ->where('status', '!=', 'done')
            ->get();

        foreach ($todaysAppointments as $appointment) {
            $timeSlot = TimeSlot::find($appointment->time_id);

            if ($timeSlot && $timeSlot->end_time <= $currentTime) {
                $appointment->delete();
            }
        }
    }

    /**
     * Cancel an appointment.
     */
    public function cancelAppointment()
    {
        $user = Auth::user();
        $doctorId = session()->get('doctor_id');


        $appointment = Appointment::where('patient_id', $user->id)
            ->where('doctor_id', $doctorId)
            ->whereNot('status', 'cancelled')
            ->whereNot('status', 'Done')
            ->delete();

        if ($appointment) {
            PatientSymptom::where('user_id', $user->id)->delete();

            return redirect()->intended('home')->with(['success' => true, 'message' => 'Appointment cancelled successfully.']);
        }

        return redirect()->back()->with(['error' => false, 'message' => 'No active appointment found.']);
    }

    /**
     * Show clinics.
     */
    public function showClinic()
    {
        $this->checkAppointment();
            $clinics = Clinic::with('city', 'district')->get()->makeHidden(['created_at', 'updated_at']);

            return view("clinic-selection", ['clinics' => $clinics]);
    }

    /**
     * Show doctors in a clinic.
     */
    public function showDoctor($clinicId)
    {
        $this->checkAppointment();
        $doctors = Doctor::withoutTrashed()
            ->where('clinic_id', $clinicId)
            ->get()
            ->makeHidden(['created_at', 'updated_at', 'deleted_at']);

        return view("select-doctor", ['doctors' => $doctors]);
    }

    /**
     * Save selected doctor to session.
     */
    public function saveDoctor(Request $request)
    {
        $doctorId = $request->query('doctor_id');
        session(['doctor_id' => $doctorId]);

        return redirect("/book-appointment");
    }

    public function checkAppointment()
    {
        $this->updateData();

        $doctorId = session()->get('doctor_id');
        $user = Auth::user();

        $appointmentDetails = Appointment::where('status', 'Pending')
            ->where('patient_id', $user->id)
            ->where('doctor_id', $doctorId)
            ->with('timeSlot')
            ->get()
            ->map(function ($appointment) {
                return [
                    'appointment_date' => $appointment->appointment_date,
                    'start_time' => $appointment->timeSlot ? $appointment->timeSlot->start_time : null,
                ];
            })->first();
        if ($appointmentDetails) {
            $symptoms = PatientSymptom::where('user_id', $user->id)->pluck('symptoms');
            $appointmentDetails['symptoms'] = $symptoms->isNotEmpty() ? json_decode($symptoms[0], true) : [];

            return view('AppointmentDetails', compact('appointmentDetails'));
        }
    }
}
