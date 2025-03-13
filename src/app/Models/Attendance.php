<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'clock_in', 'clock_out'];

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
                return 'on_break';
            }
            return 'working';
        } elseif ($this->clock_in && $this->clock_out) {
            return 'finished';
        }
        return 'not_started';
    }
}
