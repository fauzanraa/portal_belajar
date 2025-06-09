<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'desc_role' => 'User untuk mengelola data user guru dan siswa'
            ],
            [
                'name' => 'guru',
                'desc_role' => 'User yang ditujukan guru'
            ],
            [
                'name' => 'siswa',
                'desc_role' => 'User yang ditujukan siswa'
            ],
        ]);

        DB::table('admins')->insert([
            'name' => 'admin',
            'birthday' => '2000-05-22',
            'gender' => 'L'
        ]);

        DB::table('user_systems')->insert([
            'userable_id' => 1,
            'userable_name' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('admin'),
        ]);

        DB::table('role_users')->insert([
            'user_system_id' => 1,
            'role_id' => 1,
        ]);
    }
}
