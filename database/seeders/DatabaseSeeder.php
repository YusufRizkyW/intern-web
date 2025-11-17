<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\KuotaMagang;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun admin
        // User::create([
        //     'name' => 'Admin1',
        //     'email' => 'admin1@gmail.com',
        //     'password' => Hash::make('admin12345'),
        //     'role' => 'admin',
        // ]);

        // Buat kuota magang untuk beberapa bulan ke depan
        $currentYear = date('Y');
        $currentMonth = date('n');

        for ($i = 0; $i < 6; $i++) {
            $month = $currentMonth + $i;
            $year = $currentYear;

            if ($month > 12) {
                $month -= 12;
                $year++;
            }

            KuotaMagang::create([
                'tahun' => $year,
                'bulan' => $month,
                'kuota_maksimal' => 50, // Default 50 peserta per bulan
                'kuota_terisi' => 0,
                'is_active' => true,
                'catatan' => "Kuota default untuk periode " . date('F Y', mktime(0, 0, 0, $month, 1, $year)),
            ]);
        }
    }
}
