<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        $this->call(CitySeeder::class);
        $this->call(DistrictSeeder::class);
        $this->call(SymptomSeeder::class);
        $this->call(TimeSlotsSeeder::class);
        $this->call(AppointmentsSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(AppointmentsTableSeeder::class);
        $this->call(AdminTableSeeder::class);
        $this->call(DoctorSlotsSeeder::class);
    }
}
