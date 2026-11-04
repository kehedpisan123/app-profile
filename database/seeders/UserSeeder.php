<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Administrator', 'password' => 'password', 'role' => UserRole::Admin, 'email_verified_at' => now()],
        );

        User::query()->firstOrCreate(
            ['email' => 'editor@example.com'],
            ['name' => 'Editor Konten', 'password' => 'password', 'role' => UserRole::Editor, 'email_verified_at' => now()],
        );
    }
}
