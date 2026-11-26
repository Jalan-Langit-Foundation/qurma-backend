<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->error('Isi ADMIN_EMAIL dan ADMIN_PASSWORD di .env terlebih dahulu.');

            return;
        }

        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill([
            'name'     => env('ADMIN_NAME', 'Admin JLF'),
            'phone'    => env('ADMIN_PHONE', '081200000000'),
            'password' => $password,
            'role'     => 'admin',
        ])->save();
    }
}