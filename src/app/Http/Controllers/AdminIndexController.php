<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminIndexController extends Controller
{
    public function list()
    {
        $user = Auth::guard('admin')->user();
        $date = session('selected_date', Carbon::today()->format('Y-m-d'));
        $attendances = Attendance::whereDate('clock_in', $date)
            ->with(['user', 'breaks'])
            ->get()
            ->map(
                fn($attendance) => [
                    'id' => $attendance->user->id,
                    'name' => $attendance->user->name,
                    'clock_in' => $this->formatTime($attendance->clock_in),
                    'clock_out' => $this->formatTime($attendance->clock_out),
                    'break_time' => $this->formatMinutes($this->calculateBreakTime($attendance)),
                    'work_time' => $this->formatMinutes($this->calculateWorkTime($attendance)),
                ]
            );

        return view('admin.list', compact('date','attendances'));
    }

    private function formatTime($time)
    {
        return $time ? Carbon::parse($time)->format('H:i') : '-';
    }

    private function formatMinutes($minutes)
    {
        return $minutes > 0 ? sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60) : '-';
    }

    private function calculateBreakTime($attendance)
    {
        if (!$attendance->breaks || $attendance->breaks->isEmpty()) {
            return 0;
        }

        return $attendance->breaks->sum(fn($break) =>
            ($break->start_time && $break->end_time) 
                ? Carbon::parse($break->end_time)->diffInMinutes(Carbon::parse($break->start_time)) 
                : 0
        );
    }

    private function calculateWorkTime($attendance)
    {
        if (!$attendance->clock_in || !$attendance->clock_out) {
            return 0;
        }

        $totalMinutes = Carbon::parse($attendance->clock_out)
            ->diffInMinutes(Carbon::parse($attendance->clock_in));
        $breakMinutes = $this->calculateBreakTime($attendance);

        return max($totalMinutes - $breakMinutes, 0);
    }

    public function changeDate(Request $request)
    {
        $date = Carbon::parse($request->input('date'));

        if ($request->input('action') === 'prev') {
            $date->subDay();
        } elseif ($request->input('action') === 'next') {
            $date->addDay();
        }

        session(['selected_date' => $date->format('Y-m-d')]);

        return redirect()->route('admin.list');
    }
}
