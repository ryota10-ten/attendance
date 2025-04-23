<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Breaks;
use App\Models\NewAttendance;
use App\Models\NewBreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceEditController extends Controller
{
    public function detail($attendance_id)
    {
        $staff = Auth::guard('users')->user();

        $attendance = Attendance::with(['breaks'])->findOrFail($attendance_id);
        $breaksCount = $attendance->breaks->count();
        $pendingFixRequest = $attendance->fixRequests()->where('status', '0')->first();
        $new_attendance = null;
        $new_breaksCount = null;
        if($pendingFixRequest){
            $new_attendance = NewAttendance::with(['new_breaks'])
                ->where('attendance_id', $attendance_id)
                ->where('status', 0)
                ->first();
            $new_breaksCount =$new_attendance->new_breaks->count();
        }
        return view('edit',compact('staff','attendance','breaksCount','pendingFixRequest','new_attendance','new_breaksCount'));
    }

    public function store(Request $request, $id)
    {
        $staff = Auth::guard('users')->user();
        $attendance = Attendance::findOrFail($id);
        $data = $attendance->clock_in->format('Y-m-d');

        $new_clock_in = Carbon::createFromFormat('Y-m-d H:i',$data . '' . $request->input('new_clock_in'))->format('Y-m-d H:i:s');

        $new_clock_out = Carbon::createFromFormat('Y-m-d H:i',$data . '' . $request->input('new_clock_out'))->format('Y-m-d H:i:s');

        $new_attendance = [
            'attendance_id' => $id,
            'user_id'       => $staff->id,
            'new_clock_in'  => $new_clock_in,
            'new_clock_out' => $new_clock_out,
            'new_note'      => $request->input('new_note'),
            'status'        => 0,
        ];
        $fixRequest = NewAttendance::create($new_attendance);

        foreach ($request->input('new_breaks', []) as $breakInput)
        {
            if (!empty($breakInput['break_id']))
            {
                $fixRequest->new_breaks()->create([
                    'new_start_time' =>Carbon::createFromFormat('Y-m-d H:i', $data . ' ' . $breakInput['start_time']),
                    'new_end_time' =>Carbon::createFromFormat('Y-m-d H:i', $data . ' ' . $breakInput['end_time']),
                ]);
            }
        }

        foreach ($request->input('new_breaks_add', []) as $breakInput)
        {
            if (!empty($breakInput['start_time']) && !empty($breakInput['end_time']))
            {
                $fixRequest->new_breaks()->create([
                    'new_start_time' =>Carbon::createFromFormat('Y-m-d H:i', $data . ' ' . $breakInput['start_time']),
                    'new_end_time' =>Carbon::createFromFormat('Y-m-d H:i', $data . ' ' . $breakInput['end_time']),
                ]);
            }
        }

        return redirect()->back();
    }
}
