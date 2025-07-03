<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create sample users for testing
        User::create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 123',
            'rt' => '01',
            'rw' => '01',
            'password' => Hash::make('Password123!'),
            'role' => 'warga',
            'status' => 'active',
            'registered_at' => now(),
            'registered_ip' => '127.0.0.1',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'username' => 'janesmith',
            'email' => 'jane@example.com',
            'phone' => '081234567891',
            'address' => 'Jl. Sample No. 456',
            'rt' => '02',
            'rw' => '01',
            'password' => Hash::make('Password123!'),
            'role' => 'warga',
            'status' => 'active',
            'registered_at' => now(),
            'registered_ip' => '127.0.0.1',
        ]);
    }
}
