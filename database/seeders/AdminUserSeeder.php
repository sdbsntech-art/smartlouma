<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'seydoubakhayokho1@gmail.com'],
            [
                'name' => 'Seydou Bakhay Okho',
                'email' => 'seydoubakhayokho1@gmail.com',
                'password' => Hash::make('louma'),
                'role' => 'admin',
                'status' => 'active',
                'company' => 'SMART-LOUMA',
                'zone' => 'Dakar',
                'approved_at' => now(),
            ]
        );
    }
}
