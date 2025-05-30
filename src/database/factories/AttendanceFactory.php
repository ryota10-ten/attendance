<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $clockIn = $this->faker->dateTimeBetween('-1 month', 'now');
        $clockOut = (clone $clockIn)->modify('+'.rand(1, 8).' hours');
        

        return [
            'user_id' => User::factory(),
            'note' => $this->faker->sentence(),
            'clock_in' => $clockIn->format('Y-m-d H:i:s'),
            'clock_out' => $clockOut->format('Y-m-d H:i:s'),
        ];

    }
}
