<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@samity.com'],
            [
                'name'     => 'Super Admin',
                'phone'    => '01700000000',
                'role'     => 'admin',
                'password' => Hash::make('admin123'),
                'is_active' => true,
            ]
        );
    }
}
