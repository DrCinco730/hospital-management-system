<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\District;
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
    public function showBookingForm(): View
    {
        $this->updateData();

        $user = Auth::user();
        $appointmentDetails = Appointment::withoutTrashed()->whereNot('status', 'cancelled')
            ->where('patient_id', $user->id)
            ->with('timeSlot') // Eager load the TimeSlot relationship
            ->get()
            ->map(function ($appointment) {
                return [
                    'appointment_date' => $appointment->appointment_date,
                    'start_time' => $appointment->timeSlot ? $appointment->timeSlot->start_time : null,
                ];
            })->first();

        // Check if an appointment was found
        if ($appointmentDetails) {
            $symptoms = PatientSymptom::where('user_id', $user->id)->pluck('symptoms');

            // Decode the JSON encoded symptoms if necessary
            $appointmentDetails['symptoms'] = $symptoms->isNotEmpty() ? json_decode($symptoms[0], true) : [];

            return view('AppointmentDetails', compact('appointmentDetails'));
        } else {
            $symptoms = Symptom::all();

            return view('patient_symptoms', compact('symptoms'));
        }
    }

    /**
     * Show available time slots for appointment booking.
     */
    public function showTimeChoose(): View
    {
        $user = Auth::user();
        $cityId = $user->city_id;
        $districtId = $user->district_id;

        $population = District::where('id', $districtId)->value('population');
        $timeSlotDuration = $population < 25000 ? 15 : 10;

        $today = Carbon::today();
        $appointments = Appointment::where('district_id', $districtId)->whereNot('status', 'cancelled')
            ->where('city_id', $cityId)
            ->where('appointment_date', '>=', $today)
            ->get(['appointment_date', 'time_id']);

        $bookedAppointmentsByDate = $appointments->groupBy('appointment_date');

        $severityTimeFrames = [
            3 => ['start' => '8:00:00', 'end' => '12:00:00'],
            2 => ['start' => '12:00:00', 'end' => '15:00:00'],
            1 => ['start' => '15:00:00', 'end' => '17:00:00'],
        ];

        $level = PatientSymptom::withoutTrashed()->where('user_id', $user->id)->value('level');
        $startTime = $severityTimeFrames[$level]['start'];
        $endTime = $severityTimeFrames[$level]['end'];

        $timeSlots = TimeSlot::where('duration', $timeSlotDuration)
            ->where('start_time', '>=', $startTime)
            ->where('end_time', '<=', $endTime)
            ->get(['id', 'start_time', 'duration']);

        $availableSlotsByDate = [];

        foreach ($bookedAppointmentsByDate as $date => $appointments) {
            $bookedTimeIds = $appointments->pluck('time_id')->toArray();

            $availableSlots = $timeSlots->filter(function ($slot) use ($bookedTimeIds) {
                return !in_array($slot->id, $bookedTimeIds);
            })->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                ];
            });

            $dayName = Carbon::parse($date)->format('l');

            $availableSlotsByDate[Carbon::parse($date)->toDateString()] = [
                'day' => $dayName,
                'slots' => $availableSlots->values(),
            ];
        }

        // Sort the array by keys (dates)
        ksort($availableSlotsByDate);

        return view('times', compact('availableSlotsByDate'));
    }

    /**
     * Book an appointment.
     */
    public function bookSlot(Request $request): RedirectResponse
    {
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
        $cityId = $user->city_id;
        $districtId = $user->district_id;

        $timeSlot = TimeSlot::findOrFail($validatedData['time_slot']);
        $appointmentDate = Carbon::parse($validatedData['appointment_date']);

        Appointment::create([
            'patient_id' => $user->id,
            'city_id' => $cityId,
            'district_id' => $districtId,
            'appointment_date' => $appointmentDate,
            'time_id' => $timeSlot->id,
        ]);

        return redirect()->route('success')->with('success', 'Your appointment has been successfully booked!');
    }

    public function storePatientSymptoms(Request $request)
    {
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

        return $this->showTimeChoose();
    }

    public function updateData(): void
    {
        $now = Carbon::now();
        $todayDate = $now->toDateString();
        $currentTime = $now->toTimeString();

        // Fetch all appointments that are past their date
        $expiredAppointments = Appointment::where('appointment_date', '<', $todayDate)
            ->whereNull('deleted_at')
            ->where('status', '!=', 'done')
            ->get();

        foreach ($expiredAppointments as $appointment) {
            $appointment->update([ 'status' => 'done', 'deleted_at' => Carbon::now() ]);
        }

        // Fetch today's appointments to check for expiration
        $todaysAppointments = Appointment::where('appointment_date', '=', $todayDate)
            ->whereNull('deleted_at')
            ->where('status', '!=', 'done')
            ->get();

        foreach ($todaysAppointments as $appointment) {
            $timeSlot = TimeSlot::find($appointment->time_id);

            if ($timeSlot && $timeSlot->end_time <= $currentTime) {
                $appointment->update([ 'status' => 'done', 'deleted_at' => Carbon::now() ]);
            }
        }
    }

    public function cancelAppointment()
    {
        $user = Auth::user();

        $appointment = Appointment::where('patient_id', $user->id)->whereNot('status', 'cancelled')
            ->whereNull('deleted_at')->get()->first();

        if ($appointment) {
            $appointment->update(['status' => 'Cancelled', 'deleted_at' => now()]);
            PatientSymptom::where('user_id', $user->id)->delete();

            return redirect()->back()->with(['success' => true, 'message' => 'Appointment cancelled successfully.']);
        }

        return redirect()->back()->with(['error' => false, 'message' => 'No active appointment found.']);
    }
}
