<?php

namespace App\Filament\Widgets;

use App\Models\PendaftaranMagang;
use App\Models\RiwayatMagang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendaftaranStatsOverview extends BaseWidget
{
    // Opsional: kalau mau widget ini hanya tampil di panel admin saja, tapi karena kita buat di panel admin, aman
    protected static ?string $pollingInterval = '60s'; // bisa di-set '60s' kalau mau auto refresh

    protected function getStats(): array
    {
        return [
            Stat::make('Total Pendaftar', PendaftaranMagang::count())
                ->description('Semua pendaftaran yang pernah masuk')
                ->icon('heroicon-o-user-group')
                ->color('gray'),

            Stat::make('Menunggu Verifikasi', PendaftaranMagang::where('status_verifikasi', 'pending')->count())
                ->description('Butuh dicek admin')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Diterima', PendaftaranMagang::where('status_verifikasi', 'diterima')->count())
                ->description('Sudah disetujui')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Magang Selesai', RiwayatMagang::count())
                ->description('Sudah masuk riwayat')
                ->icon('heroicon-o-archive-box')
                ->color('success'),
        ];
    }
}
