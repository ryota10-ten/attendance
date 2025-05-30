<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Breaks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StaffDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_detail_name()
    {
        $user = User::factory()->create([
            'name' => '田中',
        ]);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'users')->get(route('staff.detail', ['id' => $attendance->id]));
        $response
            ->assertStatus(200)
            ->assertSee('田中');
    }

    public function test_detail_date()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->setTime(9, 0),
        ]);

        $response = $this->actingAs($user, 'users')->get(route('staff.detail', ['id' => $attendance->id]));
        $response
            ->assertStatus(200)
            ->assertSee($date->format('Y年'))
            ->assertSee($date->format('n月j日'));
    }

    public function test_detail_time()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(2),
            'clock_out' => $date->copy()->subHour(),
        ]);

        $response = $this->actingAs($user, 'users')->get(route('staff.detail', ['id' => $attendance->id]));
        $response
            ->assertStatus(200)
            ->assertSee($attendance->clock_in->format('H:i'))
            ->assertSee($attendance->clock_out->format('H:i'));
    }

    public function test_detail_break()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);
        $break1 = Breaks::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time' => $date->copy()->subHours(2),
            'end_time' => $date->copy()->subHours(1)->subMinutes(30),
        ]);

        $response = $this->actingAs($user, 'users')->get(route('staff.detail', ['id' => $attendance->id]));
        $response
            ->assertStatus(200)
            ->assertSee($break1->start_time->format('H:i'))
            ->assertSee($break1->end_time->format('H:i'));
    }
}
