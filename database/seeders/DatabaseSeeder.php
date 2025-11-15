<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun admin
        User::create([
            'name' => 'Admin1',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('admin12345'), // Ganti dengan password yang aman
            'role' => 'admin', // Atur peran sebagai admin
        ]);

        // Tambahkan seeder lainnya jika diperlukan
    }
}
