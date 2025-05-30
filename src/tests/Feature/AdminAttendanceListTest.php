<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminAttendanceListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_attendance_list()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);

        $this->actingAs($admin, 'admin');
        $response = $this -> get(route('admin.list'));

        $response
            ->assertStatus(200)
            ->assertSee($attendance->clock_in->format('H:i'));
    }

    public function test_date_now()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');
        $response = $this -> get(route('admin.list'));
        $response
            ->assertStatus(200)
            ->assertSee($date->format('Y年m月d日'));
    }

    public function test_attendance_before_date()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');
        $response = $this -> get(route('admin.list'));
        $this->actingAs($admin, 'admin')->post(route('admin.changeDate'), [
            'date' => $date->format('Y-m-d'),
            'action' => 'prev',
        ])->assertRedirect('/admin/attendance/list');
        
        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list');

        $response 
            -> assertStatus(200)
            ->assertSee($date->copy()->subDay()->format('Y年m月d日'));
    }

    public function test_attendance_next_date()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');
        $response = $this -> get(route('admin.list'));
        $this->actingAs($admin, 'admin')->post(route('admin.changeDate'), [
            'date' => $date->format('Y-m-d'),
            'action' => 'next',
        ])->assertRedirect('/admin/attendance/list');
        
        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list');

        $response 
            -> assertStatus(200)
            ->assertSee($date->copy()->addDay()->format('Y年m月d日'));
    }
}
