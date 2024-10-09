<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('appointments')->insert([
            ['id' => 1, 'patient_id' => 1, 'city_id' => 1, 'district_id' => 1, 'time_id' => 50, 'appointment_date' => '2024-10-23', 'status' => 'Pending', 'notes' => 'موعد تم إنشاؤه تلقائيًا', 'created_at' => '2024-10-01 04:25:12', 'updated_at' => '2024-10-01 04:25:12', 'deleted_at' => null],
            ['id' => 2, 'patient_id' => 2, 'city_id' => 6, 'district_id' => 27, 'time_id' => 60, 'appointment_date' => '2024-10-17', 'status' => 'Pending', 'notes' => 'موعد تم إنشاؤه تلقائيًا', 'created_at' => '2024-10-01 04:25:12', 'updated_at' => '2024-10-01 04:25:12', 'deleted_at' => null],
            ['id' => 3, 'patient_id' => 3, 'city_id' => 4, 'district_id' => 19, 'time_id' => 42, 'appointment_date' => '2024-10-08', 'status' => 'Pending', 'notes' => 'موعد تم إنشاؤه تلقائيًا', 'created_at' => '2024-10-01 14:21:24', 'updated_at' => '2024-10-01 14:21:24', 'deleted_at' => null],
            ['id' => 4, 'patient_id' => 4, 'city_id' => 3, 'district_id' => 11, 'time_id' => 50, 'appointment_date' => '2024-10-23', 'status' => 'Pending', 'notes' => 'موعد تم إنشاؤه تلقائيًا', 'created_at' => '2024-10-01 04:25:12', 'updated_at' => '2024-10-01 04:25:12', 'deleted_at' => null],
            ['id' => 5, 'patient_id' => 5, 'city_id' => 3, 'district_id' => 11, 'time_id' => 60, 'appointment_date' => '2024-10-08', 'status' => 'Pending', 'notes' => 'موعد تم إنشاؤه تلقائيًا', 'created_at' => '2024-10-01 04:25:12', 'updated_at' => '2024-10-01 04:25:12', 'deleted_at' => null],
        ]);
    }
}
