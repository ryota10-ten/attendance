<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DateAcquisitionTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    
    public function test_date_acquisition()
    {
        Carbon::setTestNow($now = Carbon::create(2025, 5, 28, 15, 0));
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'users')->get('/attendance');
        $expectedDate = $now->isoFormat('YYYY年M月D日(ddd)');
        $expectedTime = $now->format('H:i');


        $response
            ->assertStatus(200)
            ->assertSee($expectedDate)
            ->assertSee($expectedTime);
    }
}
