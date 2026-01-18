<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat Admin
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '089646542059'
        ]);

        // Buat Driver
        \App\Models\User::create([
            'name' => 'Driver Go Mentai',
            'email' => 'driver@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'driver',
            'phone' => '08123456789'
        ]);

        // Buat Customer
        \App\Models\User::create([
            'name' => 'Adedayesa',
            'email' => 'adedayesa@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '08987654321'
        ]);
    }
}
