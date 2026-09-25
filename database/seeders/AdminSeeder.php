<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) env('ADMIN_EMAIL'));
        $password = (string) env('ADMIN_PASSWORD');

        if ($email === '' || $password === '') {
            return;
        }

        $user = User::firstOrNew(['email' => mb_strtolower($email)]);
        $user->forceFill([
            'name' => env('ADMIN_NAME', 'Admin LaundryKu'),
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
        ])->save();
    }
}
