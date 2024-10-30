<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\GeneralStaff;
use App\Models\Nurse;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call(SpecialtySeeder::class);
        $this->call(CitySeeder::class);
        $this->call(DistrictSeeder::class);
        $this->call(SymptomSeeder::class);
        $this->call(TimeSlotsSeeder::class);

        $this->call(UsersTableSeeder::class);

        Clinic::factory()->count(10)->create();
        Clinic::with('district')->each(function ($clinic) {
            $population = $clinic->district->population;
//            $population = $district ? $district->population : 0;

            // حدد الحد الأقصى للأطباء بناءً على عدد السكان
            if ($population <= 10000) {
                $doctorLimit = 3;
                $nurseLimit = 5;
                $staffLimit = 3;
            } elseif ($population <= 20000) {
                $doctorLimit = 6;
                $nurseLimit = 8;
                $staffLimit = 5;
            } elseif ($population <= 30000) {
                $doctorLimit = 10;
                $nurseLimit = 12;
                $staffLimit = 8;
            } else {
                $doctorLimit = 10;
                $nurseLimit = 12;
                $staffLimit = 8;
            }

            Doctor::factory()->count($doctorLimit)->create(['clinic_id' => $clinic->id]);

            Nurse::factory()->count($nurseLimit)->create(['clinic_id' => $clinic->id]);

            GeneralStaff::factory()->count($staffLimit)->create(['clinic_id' => $clinic->id]);
        });
//        Doctor::factory()->count(20)->create();

//        $this->call(DoctorSlotsSeeder::class);


        if (Doctor::count() > 0 && User::count() > 0 && TimeSlot::count() > 0) {
            Appointment::factory()->count(20)->create();
        } else {
            throw new \Exception("Missing required data for Appointment seeding.");
        }

        Appointment::factory()->count(20)->create();


        $this->call(AdminTableSeeder::class);
    }
}
