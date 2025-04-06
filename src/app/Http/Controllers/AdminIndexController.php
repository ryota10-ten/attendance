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
        $attendances = Attendance::getAttendanceStaff($date);

        return view('admin.list', compact('date','attendances'));
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
