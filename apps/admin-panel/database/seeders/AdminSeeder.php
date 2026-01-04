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
            ['email' => 'shaleshkalyan123@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Shalesh27'),
                'role' => 'admin',
            ]
        );
    }
}
