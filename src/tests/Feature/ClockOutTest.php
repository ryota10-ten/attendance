<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClockOutTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_clock_out()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'clock_in' => $now->copy()->subHours(2),
        ]);
        $response = $this->actingAs($user, 'users')->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('退勤');
        $response = $this->actingAs($user, 'users')->post('/attendance/clock-out');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user, 'users')->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('退勤済');
    }

    public function test_attendance_clock_out()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'users')->post('/attendance/clock-in');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user, 'users')->post('/attendance/clock-out');
        
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'clock_in' => $now->copy()->toDateTimeString(),
            'clock_out' => $now->copy()->toDateTimeString(),
        ]);
        $response = $this->actingAs($user, 'users')->get('/attendance/list');
        $response
            ->assertStatus(200)
            ->assertSee($now->format('H:i'));
    }
}
