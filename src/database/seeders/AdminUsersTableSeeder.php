<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUsersTableSeeder extends Seeder
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
                'name' => 'test1',
                'email' => 'test1@example.com',
                'password' => 'testtest',

            ],
            [
                'name' => 'test2',
                'email' => 'test2@example.com',
                'password' => 'testtest',
            ],
            [
                'name' => 'test3',
                'email' => 'test3@example.com',
                'password' => 'testtest',
            ],
        ];

        foreach ($users as $user)
        {
            DB::table('admin_users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
            ]);
        }
    }
}
