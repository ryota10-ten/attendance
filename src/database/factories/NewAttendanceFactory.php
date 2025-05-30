<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\NewAttendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewAttendanceFactory extends Factory
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
            'attendance_id' => Attendance::factory(),
            'new_note' => $this->faker->sentence(),
            'new_clock_in' => $clockIn->format('Y-m-d H:i:s'),
            'new_clock_out' => $clockOut->format('Y-m-d H:i:s'),
            'status' => NewAttendance::STATUS_PENDING,
        ];
    }
}
