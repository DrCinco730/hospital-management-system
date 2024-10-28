<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        // اختيار عيادة عشوائية
        $clinic = Clinic::inRandomOrder()->first();

        return [
            'name' => $this->faker->name,
            'specialty' => $this->faker->randomElement(['Cardiology', 'Neurology', 'Pediatrics', 'Dermatology']), // التخصص الطبي
            'clinic_id' => $clinic->id,
            'experience' => $this->faker->numberBetween(1, 30),
            'email' => $this->faker->unique()->safeEmail,
            'username' => $this->faker->unique()->userName,
            'password' => '12345678',
        ];
    }
}
