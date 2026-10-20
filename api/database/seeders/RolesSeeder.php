<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'manager', 'editor'] as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }
        $email = env('ADMIN_EMAIL', 'admin@flystat.com');
        $user = User::firstOrCreate(['email' => $email], ['name' => 'Administrator', 'password' => env('ADMIN_PASSWORD', 'ChangeMe-2026!'), 'locale' => 'ru']);
        $user->syncRoles(['admin']);
    }
}
