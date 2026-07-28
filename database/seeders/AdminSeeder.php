<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin ───────────────────────────────────────────
       User::updateOrCreate(
    ['email' => 'superadmin@gmail.com'],
    [
        'first_name' => 'Super',
        'last_name'  => 'Admin',
        'password'   => 'superadmin123',  // ← plain text, cast will hash it
        'role'       => 'superadmin',
    ]
);

User::updateOrCreate(
    ['email' => 'admin@gmail.com'],
    [
        'first_name' => 'Admin',
        'last_name'  => 'PHLCI',
        'password'   => 'admin123',  // ← plain text, cast will hash it
        'role'       => 'admin',
    ]
);
    }
}