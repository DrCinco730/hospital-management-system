<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'first_name' => 'yasmin',
                'last_name' => 'ali',
                'username' => 'admin',
                'email' => 'oo491943@gmail.com',
                'password' => bcrypt("123456789"),
                'city_id' => 1,
                'district_id' => 1,
                'date_of_birth' => '2000-10-11',
                'id_number' => '123123',
                'gender' => 'female',
                'remember_token' => NULL,
                'created_at' => '2024-10-01 23:07:48',
                'updated_at' => '2024-10-01 23:07:48'
            ],
            [
                'id' => 2,
                'first_name' => 'ghada',
                'last_name' => 'ss',
                'username' => 'user1',
                'email' => 'ghada@localhost',
                'password' => bcrypt("123456789"),
                'city_id' => 6,
                'district_id' => 27,
                'date_of_birth' => '2001-10-20',
                'id_number' => '252525',
                'gender' => 'female',
                'remember_token' => NULL,
                'created_at' => '2024-10-01 23:10:05',
                'updated_at' => '2024-10-01 23:10:05'
            ],
            [
                'id' => 3,
                'first_name' => 'fat',
                'last_name' => 'al-caboudi',
                'username' => 'user2',
                'email' => 'mm@localhost',
                'password' => bcrypt("123456789"),
                'city_id' => 4,
                'district_id' => 19,
                'date_of_birth' => '2002-10-24',
                'id_number' => '12542',
                'gender' => 'female',
                'remember_token' => NULL,
                'created_at' => '2024-10-01 23:12:05',
                'updated_at' => '2024-10-01 23:12:05'
            ],
            [
                'id' => 4,
                'first_name' => 'ahmad',
                'last_name' => 'al',
                'username' => 'admin2',
                'email' => 'ahmed1@example.com',
                'password' => bcrypt("123456789"),
                'city_id' => 3,
                'district_id' => 11,
                'date_of_birth' => '1990-05-15',
                'id_number' => '1234567890',
                'gender' => 'male',
                'remember_token' => bcrypt("123456789"),
                'created_at' => '2024-10-01 04:12:15',
                'updated_at' => '2024-10-01 04:12:15'
            ],
            [
                'id' => 5,
                'first_name' => 'hh',
                'last_name' => 'ze',
                'username' => 'users',
                'email' => 'fatima1@example.com',
                'password' => bcrypt("123456789"),
                'city_id' => 3,
                'district_id' => 11,
                'date_of_birth' => '1985-08-20',
                'id_number' => '0987654321',
                'gender' => 'female',
                'remember_token' => NULL,
                'created_at' => '2024-10-01 04:12:15',
                'updated_at' => '2024-10-01 04:12:15'
            ],
        ]);
    }
}