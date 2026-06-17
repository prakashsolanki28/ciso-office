<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    // Available roles defined in the users table enum
    const ROLES = ['employee', 'admin', 'other'];

    // Permissions per role (for reference / future middleware use)
    const PERMISSIONS = [
        'admin' => [
            'incidents.view',
            'incidents.update',
            'blog.manage',
            'quiz.manage',
            'users.manage',
        ],
        'employee' => [
            'incidents.report',
            'blog.view',
            'quiz.take',
        ],
        'other' => [
            'blog.view',
        ],
    ];

    public function run(): void
    {
        // Promote any existing admin-email users to admin role
        User::where('email', 'ciso@office.com')->update(['role' => 'admin']);
    }
}
