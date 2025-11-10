<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\PendaftaranStatsOverview;
use App\Filament\Widgets\PendaftaranPerBulanChart;
use App\Filament\Widgets\TipePendaftaranPieChart;
use App\Filament\Widgets\RecentPendaftarTable;


class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $slug = 'dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            RecentPendaftarTable::class,
            PendaftaranStatsOverview::class,
            PendaftaranPerBulanChart::class,
            TipePendaftaranPieChart::class,
        ];
    }

    // Layout kolom: bar + pie bisa sejajar
    protected function getColumns(): int|string|array
    {
        return [
            'default' => 2,
            'lg' => 2,
            'xl' => 2,
        ];
    }
}
