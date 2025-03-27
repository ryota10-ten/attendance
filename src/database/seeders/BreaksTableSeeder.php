<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Breaks;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BreaksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attendances = Attendance::all(); 
        foreach ($attendances as $attendance) {
            $breakStart = Carbon::parse($attendance->clock_in)->addHours(rand(3, 5))->addMinutes(rand(0, 30));
            $breakEnd = $breakStart->copy()->addMinutes(rand(30, 60));

            Breaks::create([
                'attendance_id' => $attendance->id,
                'start_time' => $breakStart,
                'end_time' => $breakEnd,
            ]);
        }
    }
}
