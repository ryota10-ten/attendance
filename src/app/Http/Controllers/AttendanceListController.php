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

        $attendances = Attendance::getAttendancesForUser($user->id, $year, $month);

        return view('staff.list', compact('selectedMonth','attendances'));
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
