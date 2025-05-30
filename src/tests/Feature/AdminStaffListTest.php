<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminStaffListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_staff_list()
    {
        $admin = AdminUser::factory()->create();
        $users = User::factory(5)->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.list'));
        $response->assertStatus(200);
        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    public function test_staff_detail()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();
        $attendances = Attendance::factory(30)->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendanceList',['id' =>$user->id ]));
        $response->assertStatus(200);
        foreach ($attendances as $attendance) {
            $response->assertSee($attendance->clock_in->format('H:i'));
            $response->assertSee($attendance->clock_out->format('H:i'));
        }
    }

    public function test_attendance_before_month()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendanceList',['id' =>$user->id ]));
        $this->actingAs($admin, 'admin')->post(route('admin.changeMonth'), [
            'month' => $now->format('Y-m'),
            'staff_id' => $user->id,
            'action' => 'prev',])->assertRedirect(route('admin.attendanceList',['id' =>$user->id ]));
        
        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendanceList',['id' =>$user->id ]));

        $response
            -> assertStatus(200)
            ->assertSee($now->copy()->subMonth()->format('Y-m'));
    }

    public function test_attendance_next_month()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendanceList',['id' =>$user->id ]));
        $this->actingAs($admin, 'admin')->post(route('admin.changeMonth'), [
            'month' => $now->format('Y-m'),
            'staff_id' => $user->id,
            'action' => 'next',])->assertRedirect(route('admin.attendanceList',['id' =>$user->id ]));
        
        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendanceList',['id' =>$user->id ]));

        $response
            -> assertStatus(200)
            ->assertSee($now->copy()->addMonth()->format('Y-m'));
    }

    public function test_attendance_detail()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $now->copy()->subHours(3),
            'clock_out' => $now,
        ]);
        $response = $this->actingAs($admin, 'admin')->get(
            route('admin.detail', ['id' => $attendance->id])
        );
        $response 
            -> assertStatus(200)
            -> assertSee($attendance->clock_in->format('H:i'))
            -> assertSee($attendance->clock_out->format('H:i'));
    }
}
