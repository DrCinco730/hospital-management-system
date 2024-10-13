<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\PatientSymptom;
use App\Models\Symptom;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        // اختيار عيادة عشوائية
        $clinic = Clinic::inRandomOrder()->first();

        // اختيار طبيب عشوائي مرتبط بالعيادة
        $doctor = Doctor::where('clinic_id', $clinic->id)->inRandomOrder()->first();

        // اختيار مريض عشوائي
        $patient = User::inRandomOrder()->first();

        // اختيار الأعراض وتحديد مستوى الخطورة
        $symptomCount = $this->faker->numberBetween(1, 3);
        $symptoms = Symptom::inRandomOrder()->limit($symptomCount)->get();

        // تحديد مستوى الخطورة بناءً على الأعراض الطارئة
        $hasEmergency = $symptoms->contains('is_emergency', 1);
        $level = $hasEmergency ? 3 : ($symptomCount > 1 ? 2 : 1);

        // إعداد أسماء الأعراض
        $symptomNames = $symptoms->pluck('name')->toArray();

        // تحديد الأوقات المتاحة بناءً على مستوى الخطورة
        $severityTimeFrames = [
            3 => ['start' => '08:00:00', 'end' => '12:00:00'],
            2 => ['start' => '12:00:00', 'end' => '15:00:00'],
            1 => ['start' => '15:00:00', 'end' => '17:00:00'],
        ];

        $startTime = $severityTimeFrames[$level]['start'];
        $endTime = $severityTimeFrames[$level]['end'];

        // اختيار خانة زمنية متاحة بناءً على مستوى الخطورة
        $timeSlot = TimeSlot::whereBetween('start_time', [$startTime, $endTime])
            ->inRandomOrder()
            ->first();

        if (!$clinic || !$doctor || !$patient || !$timeSlot) {
            throw new \Exception("Missing required data for Appointment factory.");
        }

        // تحديد تاريخ الموعد
        $appointmentDate = $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d');

        // إنشاء سجل للأعراض المرتبطة بالمريض
        PatientSymptom::create([
            'user_id' => $patient->id,
            'symptoms' => json_encode($symptomNames),
            'level' => $level,
        ]);

        return [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time_id' => $timeSlot->id,
            'appointment_date' => $appointmentDate,
            'status' => 'Pending',
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
