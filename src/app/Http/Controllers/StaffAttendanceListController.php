<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;


class StaffAttendanceListController extends Controller
{
    public function list($id)
    {
        $admin = Auth::guard('admin')->user();
        $staff = User::find($id);
        $date = session('selected_month', Carbon::today()->format('Y-m'));
        $attendances = Attendance::getForUserInMonth($id, $date);

        return view('admin.attendance_list', compact('date','attendances','staff'));
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

        return redirect()->route('admin.attendanceList',['id' => $request->input('staff_id')]);
    }

    public function download($id)
    {
        $date = session('selected_month', Carbon::today()->format('Y-m'));
        $attendances = Attendance::getForUserInMonth($id, $date);
        $csvHeader = ['日付', '出勤', '退勤', '休憩', '合計'];

        $response = new StreamedResponse(function () use ($csvHeader,$attendances) {
            $handle = fopen('php://output', 'w');
            mb_convert_variables('SJIS-win', 'UTF-8', $csvHeader);
            fputcsv($handle, $csvHeader);
            foreach ($attendances as $attendance) {
                $row = [
                    $attendance['date'],
                    $attendance['clock_in'],
                    $attendance['clock_out'] ?? '-',
                    $attendance['break_time'],
                    $attendance['work_time'],
                ];
                mb_convert_variables('SJIS-win', 'UTF-8', $row);
                fputcsv($handle, $row);
            }
            fclose($handle);
        },200,[
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendances.csv"',
        ]);
        
        return $response;
    }
}
