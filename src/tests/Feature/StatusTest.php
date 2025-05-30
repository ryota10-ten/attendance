<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatusTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_NOT_STARTED()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'users')->get('/attendance');

        $response
            ->assertStatus(200)
            ->assertSee('勤務外');
    }

    public function test_WORKING()
    {
        $user = User::factory()->create();
        $response = $this->followingRedirects()
                ->actingAs($user, 'users')
                ->post('/attendance/clock-in');

        $response
            ->assertStatus(200)
            ->assertSee('出勤中');
    }

    public function test_ON_BREAK()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'clock_in' => now()->subHour(),
        ]);
        $attendance->breaks()->create([
            'start_time' => now()->subMinutes(10),
        ]);

        $response = $this->actingAs($user, 'users')->get('/attendance');

        $response
            ->assertStatus(200)
            ->assertSee('休憩中');
    }

    public function test_FINISHED()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'clock_in' => now()->subHour(),
            'clock_out' => now()->subHour(),
        ]);

        $response = $this->actingAs($user, 'users')->get('/attendance');

        $response
            ->assertStatus(200)
            ->assertSee('退勤済');
    }
}
