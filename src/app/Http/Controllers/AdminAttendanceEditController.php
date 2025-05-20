<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Attendance;
use App\Models\NewAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAttendanceEditController extends Controller
{
    public function detail($attendance_id)
    {
        Auth::guard('admin')->user();
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
        $staff = $attendance->user;
        return view('edit',compact('attendance','breaksCount','pendingFixRequest','new_attendance','new_breaksCount','staff'));
    }

    public function update(EditRequest $request, $id)
    {
        Auth::guard('admin')->user();
        $attendance = Attendance::with('breaks')->findOrFail($id);
        $originalDate = $attendance->clock_in->format('Y-m-d');
        $clockIn = Carbon::parse($originalDate . ' ' . $request->new_clock_in)->format('Y-m-d H:i:s');
        $clockOut = Carbon::parse($originalDate . ' ' . $request->new_clock_out)->format('Y-m-d H:i:s');

        $attendance->update([
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'note' => $request->new_note,
        ]);

        $attendance->breaks()->delete();
        if (!empty($request->new_breaks)) {
            foreach ($request->new_breaks as $break) {
                $attendance->breaks()->create([
                    'start_time' => Carbon::parse($originalDate . ' ' . $break['start_time'])->format('Y-m-d H:i:s'),
                    'end_time'   => Carbon::parse($originalDate . ' ' . $break['end_time'])->format('Y-m-d H:i:s'),
                ]);
            }
        }
        if (!empty($request->new_breaks_add)) {
            foreach ($request->new_breaks_add as $break) {
                {
                    $attendance->breaks()->create([
                        'start_time' => Carbon::parse($originalDate . ' ' . $break['start_time']),
                        'end_time'   => Carbon::parse($originalDate . ' ' . $break['end_time']),
                    ]);
                }
            }
        }

        return redirect()->back();
    }
}
