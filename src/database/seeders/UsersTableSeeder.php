<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users =
        [
            [
                'name' => 'staff1',
                'email' => 'staff1@example.com',
                'password' => 'testtest',

            ],
            [
                'name' => 'staff2',
                'email' => 'staff2@example.com',
                'password' => 'testtest',
            ],
            [
                'name' => 'staff3',
                'email' => 'staff3@example.com',
                'password' => 'testtest',
            ],
        ];

        foreach ($users as $user){
            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
                'email_verified_at' => Carbon::now(),
            ]);
        }
    }
}
