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
        $staff = Auth::guard('users')->user();
        $date = session('selected_month', Carbon::today()->format('Y-m'));
        $attendances = Attendance::getMonthlyAttendance($staff->id, $date);

        return view('staff.list', compact('date','attendances','staff'));
    }

    public function changeMonth(Request $request)
    {
        $date = Carbon::createFromFormat('Y-m', $request->input('month'))->startOfMonth();

        if ($request->input('action') === 'prev') {
            $date->subMonth();
        } elseif ($request->input('action') === 'next') {
            $date->addMonth();
        }

        session(['selected_month' => $date->format('Y-m')]);

        return redirect()->route('staff.list');
    }
}
