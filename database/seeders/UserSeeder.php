<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@pos.test',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'is_active' => true,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@pos.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Kasir Satu',
                'email' => 'kasir1@pos.test',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'is_active' => true,
            ],
            [
                'name' => 'Kasir Dua',
                'email' => 'kasir2@pos.test',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'is_active' => true,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
