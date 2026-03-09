<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@aplikatif.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );

        $admin->assignRole('superadmin');
    }
}
