<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewBreak extends Model
{
    use HasFactory;
    protected $fillable = ['new_attendance_id', 'new_start_time', 'new_end_time'];

    public function new_attendance()
    {
        return $this->belongsTo(NewAttendance::class);
    }

    protected $casts = [
        'new_start_time' => 'datetime',
        'new_end_time' => 'datetime',
    ];
}
