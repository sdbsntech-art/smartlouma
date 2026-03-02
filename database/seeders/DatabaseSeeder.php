<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer l'admin si inexistant
        User::firstOrCreate(
            ['email' => 'seydoubakhayokho1@gmail.com'],
            [
                'name'     => 'Seydou Bakhay Okho',
                'password' => Hash::make('louma'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        $this->command->info('✅ Admin créé : seydoubakhayokho1@gmail.com / louma');
    }
}
