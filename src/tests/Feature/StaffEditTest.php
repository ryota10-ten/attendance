<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Attendance;
use App\Models\Breaks;
use App\Models\NewAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StaffEditTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_edit_clock_in_errors()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);

        $response = $this->actingAs($user)->get(route('staff.detail', ['id' => $attendance->id]));
        $response = $this->actingAs($user)->post(route('staff.application', ['id' => $attendance->id]),[
            'new_clock_in' => $date->copy()->addHours(1)->format('H:i'),
            'new_clock_out' => $date->copy()->format('H:i'),
        ]);

        $response->assertSessionHasErrors([
            'new_clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_edit_break_start_errors()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);
        $this->actingAs($user);
        $response = $this->post(route('staff.application', ['id' => $attendance->id]), [
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
    
    public function test_edit_break_end_errors()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);
        $this->actingAs($user);
        $response = $this->post(route('staff.application', ['id' => $attendance->id]), [
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

    public function test_edit_note_errors()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);
        $this->actingAs($user);
        $response = $this->post(route('staff.application', ['id' => $attendance->id]), [
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

    public function test_edit_store()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);
        $this->actingAs($user);
        $response = $this->post(route('staff.application', ['id' => $attendance->id]), [
            'new_clock_in' => '07:00',
            'new_clock_out' => '10:00',
            'new_breaks_add' => [
                1 => [
                    'start_time' => '09:00', 
                    'end_time' => '09:30',
                ],
            ],
            'new_note' =>'テスト'
        ]);
        $request = NewAttendance::where('attendance_id', $attendance->id)->first();

        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin')
            ->get(route('admin.application', ['id' => $request->id]))
            ->assertStatus(200)
            ->assertSee('テスト')
            ->assertSee('09:00')
            ->assertSee('09:30');
        
        $this->actingAs($admin, 'admin')
            ->get(route('admin.request'))
            ->assertStatus(200)
            ->assertSee($attendance->clock_in->format('Y/m/d'));
    }

    public function test_edit_list_PENDING()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);
        $this->actingAs($user);
        $response = $this->post(route('staff.application', ['id' => $attendance->id]), [
            'new_clock_in' => '07:00',
            'new_clock_out' => '10:00',
            'new_breaks_add' => [
                1 => [
                    'start_time' => '09:00', 
                    'end_time' => '09:30',
                ],
            ],
            'new_note' =>'テスト'
        ]);
        $request = NewAttendance::where('attendance_id', $attendance->id)->first();

        $this->actingAs($user)
            ->get(route('staff.request'))
            ->assertStatus(200)
            ->assertSee($attendance->clock_in->format('Y/m/d'))
            ->assertSee('承認待ち');
    }

    public function test_edit_list_APPROVED()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);
        $this->actingAs($user);
        $response = $this->post(route('staff.application', ['id' => $attendance->id]), [
            'new_clock_in' => '07:00',
            'new_clock_out' => '10:00',
            'new_breaks_add' => [
                1 => [
                    'start_time' => '09:00', 
                    'end_time' => '09:30',
                ],
            ],
            'new_note' =>'テスト'
        ]);
        $request = NewAttendance::where('attendance_id', $attendance->id)->first();
        $request->status = NewAttendance::STATUS_APPROVED;
        $request->save();
        $this->actingAs($user)
            ->get(route('staff.request'))
            ->assertStatus(200)
            ->assertSee($attendance->clock_in->format('Y/m/d'))
            ->assertSee('承認済');
    }

    public function test_edit_list_detail()
    {
        Carbon::setTestNow($date = Carbon::create(2025, 5, 28, 10, 0));
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => $date->copy()->subHours(3),
            'clock_out' => $date->copy(),
        ]);
        $this->actingAs($user);
        $response = $this->post(route('staff.application', ['id' => $attendance->id]), [
            'new_clock_in' => '07:00',
            'new_clock_out' => '10:00',
            'new_breaks_add' => [
                1 => [
                    'start_time' => '09:00',
                    'end_time' => '09:30',
                ],
            ],
            'new_note' =>'テスト'
        ]);
        $request = NewAttendance::where('attendance_id', $attendance->id)->first();
        $this->actingAs($user, 'users');
        $response = $this->get(route('staff.detail', [
            'id' => $request->attendance_id
        ]));
        
        $response
            ->assertStatus(200);
    }
}
