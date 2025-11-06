<?php

namespace App\Filament\Widgets;

use App\Models\PendaftaranMagang;
use Filament\Widgets\ChartWidget;

class TipePendaftaranPieChart extends ChartWidget
{
    protected static ?string $heading = 'Perbandingan Individu vs Tim';

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $individu = PendaftaranMagang::where('tipe_pendaftaran', 'individu')->count();
        $tim      = PendaftaranMagang::where('tipe_pendaftaran', 'tim')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tipe Pendaftaran',
                    'data'  => [$individu, $tim],
                    'backgroundColor' => [
                        'rgba(37, 99, 235, 0.7)',   // biru
                        'rgba(16, 185, 129, 0.7)',  // hijau
                    ],
                ],
            ],
            'labels' => ['Individu', 'Tim / Rombongan'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
