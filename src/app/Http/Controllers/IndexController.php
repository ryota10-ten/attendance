<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Breaks;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function show()
    {
        $date = Carbon::now()->isoFormat('YYYY年M月D日(ddd)');
        $time = Carbon::now()->format('H:i');
        $user = Auth::guard('users')->user();

        $attendance = Attendance::getTodaysRecord($user->id);

        $status = Attendance::NOT_STARTED;

        if ($attendance) {
            if (is_null($attendance->clock_out)) {
                $onBreak = Breaks::where('attendance_id', $attendance->id)
                    ->whereNull('end_time')
                    ->exists();

                $status = $onBreak ? Attendance::ON_BREAK : Attendance::WORKING;
            }
        }

        return view('staff.index', compact('date', 'time', 'status'));
    }

    public function clockIn()
    {
        $userId = Auth::guard('users')->id();

        $latestAttendance = Attendance::getTodaysRecord($userId);

        if ($latestAttendance && is_null($latestAttendance->clock_out)) {
            return redirect()->route('home.show');
        }

        Attendance::create([
            'user_id' => $userId,
            'clock_in' => Carbon::now(),
        ]);

        return redirect()->route('home.show');
    }

    public function clockOut()
    {
        $attendance = Attendance::where('user_id', Auth::guard('users')->id())
            ->whereDate('clock_in', Carbon::today())
            ->whereNull('clock_out')
            ->latest('clock_in')
            ->first();

        if ($attendance) {
            $attendance->update(['clock_out' => Carbon::now()]);
        }

        return redirect()->route('home.show');
    }

    public function startBreak()
    {
        $attendance = Attendance::where('user_id', Auth::guard('users')->id())
            ->whereDate('clock_in', Carbon::today())
            ->whereNull('clock_out')
            ->first();

        if ($attendance) {
            Breaks::create([
                'attendance_id' => $attendance->id,
                'start_time' => Carbon::now(),
            ]);
        }

        return redirect()->route('home.show');
    }

    public function endBreak()
    {
        $break = Breaks::whereHas('attendance', function ($query) {
                $query->where('user_id', Auth::id())->whereDate('clock_in', Carbon::today())->whereNull('clock_out');
            })
            ->whereNull('end_time')
            ->first();
        
        if ($break) {
            $break->update(['end_time' => Carbon::now()]);
        }

        return redirect()->route('home.show');
    }
}
