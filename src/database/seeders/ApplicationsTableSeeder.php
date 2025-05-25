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
    private const ATTENDANCE_COUNT = 20;
    private const BREAK_COUNT = 20;

    private const CLOCK_IN_SUB_MIN = 5;
    private const CLOCK_IN_SUB_MAX = 15;
    private const CLOCK_OUT_ADD_MIN = 5;
    private const CLOCK_OUT_ADD_MAX = 20;

    private const BREAK_START_SUB_MINUTES = 5;
    private const BREAK_END_ADD_MINUTES = 10;

    public function run()
    {
        $attendances = Attendance::inRandomOrder()->take(self::ATTENDANCE_COUNT)->get();
        $newAttendanceMap = [];

        foreach ($attendances as $attendance) 
        {
            $originalClockIn = Carbon::parse($attendance->clock_in);
            $originalClockOut = Carbon::parse($attendance->clock_out);
            $newClockIn = $originalClockIn->copy()->subMinutes(rand(self::CLOCK_IN_SUB_MIN, self::CLOCK_IN_SUB_MAX));
            $newClockOut = $originalClockOut->copy()->addMinutes(rand(self::CLOCK_OUT_ADD_MIN, self::CLOCK_OUT_ADD_MAX));
            $newAttendance = NewAttendance::create([
                'attendance_id' => $attendance->id,
                'new_clock_in' => $newClockIn,
                'new_clock_out' => $newClockOut,
                'new_note' => 'テスト用の修正申請',
                'status' => NewAttendance::STATUS_PENDING
            ]);
            $newAttendanceMap[$attendance->id] = $newAttendance->id;
        }

        $breaks = Breaks::inRandomOrder()->take(self::BREAK_COUNT)->get();

        foreach ($breaks as $break)
        {
            $attendanceId = $break->attendance_id;
            if (isset($newAttendanceMap[$attendanceId])) {
                NewBreak::create([
                    'new_attendance_id' => $newAttendanceMap[$attendanceId],
                    'new_start_time' => Carbon::parse($break->start_time)->subMinutes(self::BREAK_START_SUB_MINUTES),
                    'new_end_time' => Carbon::parse($break->end_time)->addMinutes(self::BREAK_END_ADD_MINUTES),
                ]);
            }
        }
    }
}
