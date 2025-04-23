<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NewAttendance extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'attendance_id', 'new_clock_in', 'new_clock_out','new_note','status'];

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

    protected $casts = [
        'status' => 'boolean',
        'new_clock_in' => 'datetime',
        'new_clock_out' => 'datetime',
    ];

}
