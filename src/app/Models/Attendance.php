<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'clock_in', 'clock_out','note'];

    const NOT_STARTED = 'not_started';
    const ON_BREAK = 'on_break';
    const WORKING = 'working';
    const FINISHED = 'finished';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(Breaks::class);
    }

    public function fixRequests()
    {
        return $this->hasMany(NewAttendance::class);
    }

    public function getStatusAttribute()
    {
        if ($this->clock_in && !$this->clock_out) {
            if ($this->breaks()->whereNull('end_time')->exists()) {
                return self::ON_BREAK ;
            }
            return self::WORKING;
        } elseif ($this->clock_in && $this->clock_out) {
            return self::FINISHED;
        }
        return self::NOT_STARTED;
    }

    public static function getTodaysRecord($user_id)
    {
        return  Attendance::where('user_id', $user_id)
            ->whereDate('clock_in', Carbon::today())
            ->latest('clock_in')
            ->first();
    }

    public static function getAttendanceStaff($date)
    {
        return self::whereDate('clock_in', $date)
            ->with(['user', 'breaks'])
            ->get()
            ->map(fn($attendance) => $attendance->formatForAttendanceList());
    }

    public function formatForAttendanceList()
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'clock_in' => self::formatTime($this->clock_in),
            'clock_out' => self::formatTime($this->clock_out),
            'break_time' => self::formatMinutes(self::calculateBreakTime($this)),
            'work_time' => self::formatMinutes(self::calculateWorkTime($this)),
        ];
    }

    public static function getMonthlyAttendance($userId, $yearMonth)
    {
        $carbonDate = Carbon::createFromFormat('Y-m', $yearMonth);
        $start = $carbonDate->copy()->startOfMonth();
        $end = $carbonDate->copy()->endOfMonth();
        return Attendance::where('user_id', $userId)
            ->whereBetween('clock_in', [$start, $end])
            ->with(['breaks'])
            ->get()
            ->map(fn($attendance) => $attendance->formatForList());
    }

    public static function getForUserInMonth($userId, $yearMonth)
    {
        $date = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
        $start = $date->copy();
        $end = $date->copy()->endOfMonth();

        return self::where('user_id', $userId)
            ->whereBetween('clock_in', [$start, $end])
            ->with(['breaks'])
            ->get()
            ->map(fn($attendance) => $attendance->formatForList());
    }

    public function formatForList()
    {
        return [
            'id' => $this->id,
            'date' => self::formatDayName($this->clock_in),
            'clock_in' => self::formatTime($this->clock_in),
            'clock_out' => self::formatTime($this->clock_out),
            'break_time' => self::formatMinutes(self::calculateBreakTime($this)),
            'work_time' => self::formatMinutes(self::calculateWorkTime($this)),
        ];
    }

    private static function formatDayName($date)
    {
        if (!$date) {
            return '-';
        }

        $carbonDate = Carbon::parse($date);
        $shortDay = mb_substr($carbonDate->dayName, 0, 1);

        return $carbonDate->format('m/d') . '（' . $shortDay . '）';
    }

    private static function formatTime($time)
    {
        return $time ? Carbon::parse($time)->format('H:i') : '-';
    }

    private static function formatMinutes($minutes)
    {
        return $minutes > 0 ? sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60) : '-';
    }

    private static function calculateBreakTime($attendance)
    {
        if (!$attendance->breaks || $attendance->breaks->isEmpty()) {
            return 0;
        }

        return $attendance->breaks->sum(fn($break) =>
            ($break->start_time && $break->end_time) 
                ? Carbon::parse($break->end_time)->diffInMinutes(Carbon::parse($break->start_time)) 
                : 0
        );
    }

    private static function calculateWorkTime($attendance)
    {
        if (!$attendance->clock_in || !$attendance->clock_out) {
            return 0;
        }

        $totalMinutes = Carbon::parse($attendance->clock_out)
            ->diffInMinutes(Carbon::parse($attendance->clock_in));
        $breakMinutes = self::calculateBreakTime($attendance);

        return max($totalMinutes - $breakMinutes, 0);
    }

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];
}
