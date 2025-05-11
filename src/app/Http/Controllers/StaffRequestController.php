<?php

namespace App\Http\Controllers;

use App\Models\NewAttendance;
use Illuminate\Support\Facades\Auth;

class StaffRequestController extends Controller
{
    public function show()
    {
        $user = Auth::guard('users')->user();
        $unApproved__lists = NewAttendance::forUserAndStatus($user->id, NewAttendance::STATUS_PENDING)->get();
        $approved__lists = NewAttendance::forUserAndStatus($user->id, NewAttendance::STATUS_APPROVED)->get();
        return view ('staff.request',compact('unApproved__lists','approved__lists'));
    }
}
