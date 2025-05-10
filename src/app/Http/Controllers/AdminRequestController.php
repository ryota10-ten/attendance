<?php

namespace App\Http\Controllers;

use App\Models\NewAttendance;
use Illuminate\Support\Facades\Auth;

class AdminRequestController extends Controller
{
    public function show()
    {
        $user = Auth::guard('admin')->user();
        $unapproved__lists = NewAttendance::with(['user'])
            ->where('status', NewAttendance::STATUS_PENDING)
            ->get();
        $approved__lists = NewAttendance::with(['user'])
            ->where('status', NewAttendance::STATUS_APPROVED)
            ->get();
        return view ('admin.request',compact('unapproved__lists','approved__lists'));
    }
}
