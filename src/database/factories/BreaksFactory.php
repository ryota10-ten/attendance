<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreaksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $clockIn = $this->faker->dateTimeBetween('-1 month', 'now');
        $clockOut = (clone $clockIn)->modify('+' . rand(3, 9) . ' hours');

        $start = $this->faker->dateTimeBetween($clockIn->modify('+1 hour'), (clone $clockOut)->modify('-1 hour'));
        $end = (clone $start)->modify('+' . rand(15, 60) . ' minutes');

        $attendance = Attendance::factory()->create([
            'clock_in' => $clockIn->format('Y-m-d H:i:s'),
            'clock_out' => $clockOut->format('Y-m-d H:i:s'),
        ]);
        
        return [
            'attendance_id' => $attendance->id,
            'start_time' => $start->format('Y-m-d H:i:s'),
            'end_time' => $end->format('Y-m-d H:i:s'),
        ];
    }
}
