<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();

        $users = [
            [
                'id' => 'b0eedd34-7705-4f36-8b13-c30673d058be',
                'name' => 'Burhan Mafazi',
                'username' => 'bmafazi',
                'email' => 'burhanburdev@gmail.com',
                'phone' => '081234567890',
                'password' => Hash::make('burhan123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'created_by' => null,
                'updated_by' => null,
            ],
            [
                'id' => 'b444c51b-e857-416f-b648-1c1e9b4829a9',
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'created_by' => null,
                'updated_by' => null,
            ],
            [
                'id' => 'c1234567-89ab-4cde-f012-3456789abcde',
                'name' => 'Mechanical Engineer',
                'username' => 'me',
                'email' => 'me@example.com',
                'phone' => '081234567892',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'created_by' => null,
                'updated_by' => null,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
