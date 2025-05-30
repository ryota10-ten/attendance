<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_attendance_list()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');
        $response -> assertStatus(200);
        $response->assertSee($attendance->clock_in->format('H:i'));
    }

    public function test_attendance_month()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $attendances = Attendance::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($user)->get('/attendance/list');

        $response 
            -> assertStatus(200)
            ->assertSee($now->format('Y-m'));
    }

    public function test_attendance_before_month()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/attendance/list');
        $this->actingAs($user)->post(route('staff.changeMonth'), [
            'month' => $now->format('Y-m'), 
            'staff_id' => $user->id,
            'action' => 'prev',])->assertRedirect('/attendance/list');
        
        $response = $this->actingAs($user)->get('/attendance/list');

        $response 
            -> assertStatus(200)
            ->assertSee($now->copy()->subMonth()->format('Y-m'));
    }

    public function test_attendance_next_month()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/attendance/list');
        $this->actingAs($user)->post(route('staff.changeMonth'), [
            'month' => $now->format('Y-m'),
            'staff_id' => $user->id,
            'action' => 'next',])->assertRedirect('/attendance/list');
        
        $response = $this->actingAs($user)->get('/attendance/list');

        $response 
            -> assertStatus(200)
            ->assertSee($now->copy()->addMonth()->format('Y-m'));
    }

    public function test_attendance_detail()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $attendances = Attendance::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)->get('/attendance/$attendance->id');
        $response -> assertStatus(200);
    }
}
