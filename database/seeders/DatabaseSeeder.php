<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        $this->call(CitySeeder::class);
        $this->call(DistrictSeeder::class);
        $this->call(SymptomSeeder::class);
        $this->call(TimeSlotsSeeder::class);

        $this->call(UsersTableSeeder::class);

        Clinic::factory()->count(10)->create();
        Clinic::all()->each(function ($clinic) {
            Doctor::factory()->count(rand(1, 3))->create([
                'clinic_id' => $clinic->id,
            ]);
        });
//        Doctor::factory()->count(20)->create();

        $this->call(DoctorSlotsSeeder::class);


        if (Doctor::count() > 0 && User::count() > 0 && TimeSlot::count() > 0) {
            Appointment::factory()->count(20)->create();
        } else {
            throw new \Exception("Missing required data for Appointment seeding.");
        }

        Appointment::factory()->count(20)->create();


        $this->call(AdminTableSeeder::class);
    }
}
