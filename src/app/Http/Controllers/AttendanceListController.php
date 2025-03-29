<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceListController extends Controller
{
    public function list()
    {
        $user = Auth::guard('users')->user();
        $selectedMonth = session('selected_date', Carbon::now()->format('Y-m'));

        [$year, $month] = explode('-', $selectedMonth);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('clock_in', $year)
            ->whereMonth('clock_in', $month)
            ->with(['breaks'])
            ->get()
            ->map(
                fn($attendance) => [
                    'id' => $attendance -> id,
                    'date' => $this->format($attendance->clock_in),
                    'clock_in' => $this->formatTime($attendance->clock_in),
                    'clock_out' => $this->formatTime($attendance->clock_out),
                    'break_time' => $this->formatMinutes($this->calculateBreakTime($attendance)),
                    'work_time' => $this->formatMinutes($this->calculateWorkTime($attendance)),
                ]
            );

        return view('staff.list', compact('selectedMonth','attendances'));
    }

    private function format($date)
    {
        if (!$date) {
            return '-';
        }

        $carbonDate = Carbon::parse($date);
        $shortDay = mb_substr($carbonDate->dayName, 0, 1);

        return $carbonDate->format('m/d') . '（' . $shortDay . '）';
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
        $selectedMonth = session('selected_month', Carbon::now()->format('Y-m'));

        if ($request->has('month')) {
            $selectedMonth = $request->input('month');
        }

        $date = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();

        if ($request->input('action') === 'prev') {
            $date->subMonth();
        } elseif ($request->input('action') === 'next') {
            $date->addMonth();
        }

        session(['selected_date' => $date->format('Y-m')]);

        return redirect()->route('staff.list');
    }
}
