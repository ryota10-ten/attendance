<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewAttendance extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'attendance_id', 'new_clock_in', 'new_clock_out', 'new_note', 'status'];

    protected $casts = [
        'status' => 'boolean',
        'new_clock_in' => 'datetime',
        'new_clock_out' => 'datetime',
    ];

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function new_breaks()
    {
        return $this->hasMany(NewBreak::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public static function fetchPendingByAttendanceId($attendance_id)
    {
        return self::with('new_breaks')
            ->where('attendance_id', $attendance_id)
            ->where('status', self::STATUS_PENDING)
            ->first();
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->with('user')->where('status', $status);
    }

    public function scopeForUserAndStatus($query, $userId, $status)
    {
        return $query->where('user_id', $userId)
                    ->where('status', $status)
                    ->with('user');
    }

    public function approveAndApplyTo(Attendance $attendance)
    {
        $this->update(['status' => self::STATUS_APPROVED]);

        $originalDate = $attendance->clock_in->format('Y-m-d');

        $clockIn = $originalDate . ' ' . $this->new_clock_in->format('H:i:s');
        $clockOut = $originalDate . ' ' . $this->new_clock_out->format('H:i:s');

        $attendance->update([
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'note' => $this->new_note,
        ]);

        $attendance->breaks()->delete();

        foreach ($this->new_breaks as $break) {
            $attendance->breaks()->create([
                'start_time' => $originalDate . ' ' . $break->new_start_time->format('H:i:s'),
                'end_time'   => $originalDate . ' ' . $break->new_end_time->format('H:i:s'),
            ]);
        }
    }
}
