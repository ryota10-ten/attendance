<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Attendance;
use App\Models\NewAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminAttendanceEditTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_application_list_pending()
    {
        Carbon::setTestNow(Carbon::create(2025, 5, 28, 12, 0));
        $admin = AdminUser::factory()->create();
        $users = User::factory(3)->create();
        foreach ($users as $user) {
            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);
            NewAttendance::factory()->create([
                'attendance_id' => $attendance->id,
                'status' => NewAttendance::STATUS_PENDING,
                'new_clock_in' => '08:00:00',
                'new_clock_out' => '17:00:00',
            ]);
        }
        $response = $this->actingAs($admin, 'admin')->get(route('admin.request'));
        foreach ($users as $user) {
            $response
                ->assertSee($user->name);
        }
        $response->assertSee('承認待ち');
    }

    public function test_application_list_approve()
    {
        Carbon::setTestNow(Carbon::create(2025, 5, 28, 12, 0));
        $admin = AdminUser::factory()->create();
        $users = User::factory(3)->create();
        foreach ($users as $user) {
            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);
            NewAttendance::factory()->create([
                'attendance_id' => $attendance->id,
                'status' => NewAttendance::STATUS_APPROVED,
                'new_clock_in' => '08:00:00',
                'new_clock_out' => '17:00:00',
            ]);
        }
        $response = $this->actingAs($admin, 'admin')->get(route('admin.request'));
        foreach ($users as $user) {
            $response
                ->assertSee($user->name);
        }
        $response->assertSee('承認済');
    }

    public function test_application_list_detail()
    {
        Carbon::setTestNow(Carbon::create(2025, 5, 28, 12, 0));
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);
        $newAttendance = NewAttendance::factory()->create([
            'attendance_id' => $attendance->id,
            'status' => NewAttendance::STATUS_PENDING,
            'new_clock_in' => '08:00:00',
            'new_clock_out' => '17:00:00',
            'new_note' => '詳細画面テスト',
        ]);

        $response = $this->get(route('admin.application', ['id' => $newAttendance->id]));

        $response
            ->assertStatus(200)
            ->assertSee('08:00')
            ->assertSee('17:00')
            ->assertSee('詳細画面テスト'); 
    }

    public function test_application_approve()
    {
        Carbon::setTestNow(Carbon::create(2025, 5, 28, 12, 0));
        $admin = AdminUser::factory()->create();
        $this->actingAs($admin, 'admin');
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'note' => '元の備考',
        ]);
        $newAttendance = NewAttendance::factory()->create([
            'attendance_id' => $attendance->id,
            'status' => NewAttendance::STATUS_PENDING,
            'new_clock_in' => '08:00:00',
            'new_clock_out' => '17:00:00',
            'new_note' => '修正された備考',
        ]);

        $response = $this->actingAs($admin, 'admin')->post(
            route('admin.approval', ['id' => $attendance->id]),
            [
                'new_attendance_id' => $newAttendance->id,
            ]
        );
        $response->assertRedirect();
        $this->assertDatabaseHas('new_attendances', [
            'id' => $newAttendance->id,
            'status' => NewAttendance::STATUS_APPROVED,
        ]);
    }

}
