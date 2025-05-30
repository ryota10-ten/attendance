<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminApproveTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_attendance_detail()
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
        $response = $this -> get(route('admin.detail', ['id'=> $attendance->id]));
        $response
            ->assertStatus(200)
            ->assertSee($attendance->clock_in->format('H:i'));
    }

    public function test_attendance_edit_clock_in()
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
        $response = $this -> get(route('admin.detail', ['id'=> $attendance->id]));
        $response = $this -> post(route('admin.update', ['id' => $attendance->id]),[
            'new_clock_in' => $date->copy()->addHours(1)->format('H:i'),
            'new_clock_out' => $date->copy()->format('H:i'),
        ]);

        $response->assertSessionHasErrors([
            'new_clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_attendance_edit_break_start()
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
        $response = $this->post(route('admin.update', ['id' => $attendance->id]), [
            'new_clock_in' => '07:00',
            'new_clock_out' => '10:00',
            'new_breaks_add' => [
                1 => [
                    'start_time' => '11:00', 
                    'end_time' => '11:30',
                ],
            ],
            'new_note' => 'テスト',
        ]);
        $response->assertSessionHasErrors('breaks');
        $this->assertContains(
            '休憩時間が勤務時間外です',
            session('errors')->get('breaks')
        );
    }

    public function test_attendance_edit_break_end()
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
        $response = $this->post(route('admin.update', ['id' => $attendance->id]), [
            'new_clock_in' => '07:00',
            'new_clock_out' => '10:00',
            'new_breaks_add' => [
                1 => [
                    'start_time' => '09:30', 
                    'end_time' => '10:30',
                ],
            ],
            'new_note' => 'テスト',
        ]);
        $response->assertSessionHasErrors('breaks');
        $this->assertContains(
            '休憩時間が勤務時間外です',
            session('errors')->get('breaks')
        );
    }

    public function test_attendance_edit_note()
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
        $response = $this->post(route('admin.update', ['id' => $attendance->id]), [
            'new_clock_in' => '07:00',
            'new_clock_out' => '10:00',
            'new_breaks_add' => [
                1 => [
                    'start_time' => '09:30', 
                    'end_time' => '10:30',
                ],
            ],
        ]);
        $response->assertSessionHasErrors('new_note');
        $this->assertContains(
            '備考を記入してください',
            session('errors')->get('new_note')
        );
    }
}
