<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'clock_in', 'clock_out'];

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
}
