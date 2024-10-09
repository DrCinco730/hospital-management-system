<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;
use App\Models\City;
use App\Models\District;
use App\Models\TimeSlot;
use Carbon\Carbon;

class AppointmentsSeeder extends Seeder
{
    public function run()
    {
        // Ensure related tables have data

        // 1. Get all users
        $users = User::all();

        // 2. Get all cities and districts
        $cities = City::all();
        $districts = District::all();

        // 3. Get all available time slots
        $timeSlots = TimeSlot::all();

        if ($timeSlots->isEmpty()) {
            $this->command->info('No data in time_slots table. Please populate it before running the seeder.');
            return;
        }

        // 4. Create appointments for each user
        foreach ($users as $user) {
            // Select a random city and district within that city
            $city = $cities->random();
            $district = $districts->where('city_id', $city->id)->random();

            // Select a random time slot
            $timeSlot = $timeSlots->random();

            // Create an appointment
            Appointment::create([
                'patient_id' => $user->id,
                'city_id' => $city->id,
                'district_id' => $district->id,
                'time_id' => $timeSlot->id,
                'appointment_date' => Carbon::now()->addDays(rand(1, 30))->format('Y-m-d'),
                'status' => 'Pending',
                'notes' => 'Automatically generated appointment',
            ]);
        }

        $this->command->info('Appointments created successfully.');
    }
}
