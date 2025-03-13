<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Breaks;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function show()
    {
        $date = Carbon::now()->isoFormat('YYYY年M月D日(ddd)');
        $time = Carbon::now()->format('H:i');
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('clock_in', Carbon::today())
            ->first();

        return view('index',compact('date','time','attendance'));
    }

    public function clockIn()
    {
        $userId = Auth::id();

        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereDate('clock_in', Carbon::today())
            ->first();

        Attendance::create([
            'user_id' => $userId,
            'clock_in' => Carbon::now(),
        ]);

        return redirect()->route('home.show');
    }

    public function clockOut()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('clock_in', Carbon::today())
            ->whereNull('clock_out')
            ->first();
        $attendance->update(['clock_out' => Carbon::now()]);

        return redirect()->route('home.show');
    }

    public function startBreak()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('clock_in', Carbon::today())
            ->whereNull('clock_out')
            ->first();
        Breaks::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::now(),
        ]);

        return redirect()->route('home.show');
    }

    public function endBreak()
    {
        $break = Breaks::whereHas('attendance', function ($query) {
                $query->where('user_id', Auth::id())->whereDate('clock_in', Carbon::today())->whereNull('clock_out');
            })
            ->whereNull('end_time')
            ->first();
        $break->update(['end_time' => Carbon::now()]);

        return redirect()->route('home.show');
    }
}
