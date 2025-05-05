<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Breaks;
use App\Models\NewAttendance;
use App\Models\NewBreak;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ApplicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attendances = Attendance::inRandomOrder()->take(20)->get();
        $newAttendanceMap = [];

        foreach ($attendances as $attendance) 
        {
            $originalClockIn = Carbon::parse($attendance->clock_in);
            $originalClockOut = Carbon::parse($attendance->clock_out);
            $newClockIn = $originalClockIn->copy()->subMinutes(rand(5, 15));
            $newClockOut = $originalClockOut->copy()->addMinutes(rand(5, 20));
            $newAttendance = NewAttendance::create([
                'attendance_id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'new_clock_in' => $newClockIn,
                'new_clock_out' => $newClockOut,
                'new_note' => 'テスト用の修正申請',
                'status' =>'0'
            ]);
            $newAttendanceMap[$attendance->id] = $newAttendance->id;
        }
        
        $breaks = Breaks::inRandomOrder()->take(20)->get();

        foreach ($breaks as $break)
        {
            $attendanceId = $break->attendance_id;
            if (isset($newAttendanceMap[$attendanceId])) {
                NewBreak::create([
                    'new_attendance_id' => $newAttendanceMap[$attendanceId],
                    'new_start_time' => Carbon::parse($break->start_time)->subMinutes(5),
                    'new_end_time' => Carbon::parse($break->end_time)->addMinutes(10),
                ]);
            }
        }
    }
}
