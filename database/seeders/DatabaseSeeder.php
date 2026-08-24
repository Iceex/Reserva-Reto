<?php

namespace Database\Seeders;

use App\Models\Mesa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Usuario Demo', 'password' => Hash::make('password')]
        );

        foreach (['A', 'B', 'C', 'D'] as $ubicacion) {
            foreach (range(1, 3) as $numero) {
                Mesa::updateOrCreate(
                    ['ubicacion' => $ubicacion, 'numero' => $numero],
                    ['capacidad' => $numero <= 3 ? 2 : 4]
                );
            }
        }
    }
}
