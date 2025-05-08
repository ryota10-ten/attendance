<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function detail($_id)
    {
        $staff = Auth::guard('users')->user();

        $attendance = Attendance::with(['breaks'])->findOrFail($attendance_id);
        $breaksCount = $attendance->breaks->count();
        $pendingFixRequest = $attendance->fixRequests()
            ->where('status', NewAttendance::STATUS_PENDING)
            ->first();
        $new_attendance = null;
        $new_breaksCount = null;
        if($pendingFixRequest){
            $new_attendance = NewAttendance::fetchPendingByAttendanceId($attendance_id);
            $new_breaksCount =$new_attendance->new_breaks->count();
        }
        return view('edit',compact('staff','attendance','breaksCount','pendingFixRequest','new_attendance','new_breaksCount'));
    }
}
