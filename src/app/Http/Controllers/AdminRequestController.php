<?php

namespace App\Http\Controllers;

use App\Models\NewAttendance;
use Illuminate\Support\Facades\Auth;

class AdminRequestController extends Controller
{
    public function show()
    {
        $user = Auth::guard('admin')->user();
        $unApproved__lists = NewAttendance::withStatus(NewAttendance::STATUS_PENDING)->get();
        $approved__lists = NewAttendance::withStatus(NewAttendance::STATUS_APPROVED)->get();
        return view ('admin.request',compact('unApproved__lists','approved__lists'));
    }
}
