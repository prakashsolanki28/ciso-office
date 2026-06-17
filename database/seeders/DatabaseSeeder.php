<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Ciso Office',
            'email' => 'ciso@office.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Demo employee for the quiz-taking portal (pre-verified & active).
        User::factory()->create([
            'name' => 'Demo Employee',
            'email' => 'employee@office.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->call([
            RoleAndPermissionSeeder::class,
        ]);
    }
}
