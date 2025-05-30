<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BreakTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_break_function()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'clock_in' => $now->copy()->subHours(2),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('休憩入');
        
        $response = $this->actingAs($user)->post('/attendance/break-start');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('休憩中');
    }

    public function test_break_start_many_times()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'clock_in' => $now->copy()->subHours(2),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('休憩入');

        $response = $this->actingAs($user)->post('/attendance/break-start');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user)->post('/attendance/break-end');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('休憩入');
    }

    public function test_break_end()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'clock_in' => $now->copy()->subHours(2),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('休憩入');

        $response = $this->actingAs($user)->post('/attendance/break-start');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user)->post('/attendance/break-end');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('出勤中');
    }

    public function test_break_end_many_times()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'clock_in' => $now->copy()->subHours(2),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('休憩入');

        $response = $this->actingAs($user)->post('/attendance/break-start');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user)->post('/attendance/break-end');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user)->post('/attendance/break-start');
        $response->assertRedirect('/attendance');
        $response = $this->actingAs($user)->get('/attendance');
        $response
            ->assertStatus(200)
            ->assertSee('休憩戻');
    }

    public function test_break_time()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'clock_in' => $now->copy()->subHours(2),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $breakStart = $now->copy()->subMinutes(10);
        Carbon::setTestNow($breakStart);
        $this->actingAs($user)->post('/attendance/break-start');
        $breakEnd = $now->copy()->subMinutes(5);
        Carbon::setTestNow($breakEnd);
        $this->actingAs($user)->post('/attendance/break-end');
        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'start_time' => $breakStart->toDateTimeString(),
            'end_time' => $breakEnd->toDateTimeString(),
        ]);
        Carbon::setTestNow($now);
        $response = $this->actingAs($user)->get('/attendance/list');
        $response
            ->assertStatus(200)
            ->assertSee('00:05', false); 
    }
}
