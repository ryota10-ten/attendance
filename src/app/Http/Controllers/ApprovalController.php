<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\NewAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function show($id)
    {
        $admin = Auth::guard('admin')->user();

        $new_attendance = NewAttendance::with(['new_breaks','attendance.user'])->findOrFail($id);
        $breaksCount = $new_attendance->new_breaks->count();

        return view('admin.approval',compact('admin','breaksCount','new_attendance'));
    }

    public function approval(Request $request, $attendance_id)
    {
        Auth::guard('admin')->user();

        $new_attendance = NewAttendance::with('new_breaks')->findOrFail($request->new_attendance_id);
        $attendance = Attendance::with('breaks')->findOrFail($attendance_id);

        $new_attendance->approveAndApplyTo($attendance);

        return redirect()->back();
    }
}
