<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::forceCreate([
            'name'     => 'Admin JLF',
            'email'    => 'admin@qurma.test',
            'phone'    => '081200000000',
            'password' => 'GantiPasswordIni123',
            'role'     => 'admin',
        ]);
    }
}