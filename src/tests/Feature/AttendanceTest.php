<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_attendance_function()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('出勤');

        $response = $this->actingAs($user)->post('/attendance/clock-in');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('出勤中');
    }

    public function test_attendance_FINISHED()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'clock_in' => $now->copy()->subHours(2),
            'clock_out' => $now->copy()->subHour(),
        ]);
        $response = $this->actingAs($user)->get('/attendance');

        $response
            ->assertStatus(200)
            ->assertDontSee('出勤');
    }

    public function test_attendance_clock_in()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/attendance/clock-in');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'clock_in' => $now->toDateTimeString(),
        ]);
        $response = $this->actingAs($user)->get('/attendance/list');
        $response
            ->assertStatus(200)
            ->assertSee($now->format('H:i'));
    }
}
