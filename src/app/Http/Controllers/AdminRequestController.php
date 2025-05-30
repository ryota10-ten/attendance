<?php

namespace App\Http\Controllers;

use App\Models\NewAttendance;
use Illuminate\Support\Facades\Auth;

class AdminRequestController extends Controller
{
    public function show()
    {
        $user = Auth::guard('admin')->user();
        $unApproved__lists = NewAttendance::where('status',NewAttendance::STATUS_PENDING)
        ->with('attendance.user')->get();

        $approved__lists = NewAttendance::where('status',NewAttendance::STATUS_APPROVED)
        ->with('attendance.user')->get();
        return view ('admin.request',compact('unApproved__lists','approved__lists'));
    }
}
