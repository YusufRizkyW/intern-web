<?php

namespace App\Filament\Widgets;

use App\Models\PendaftaranMagang;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PendaftaranPerBulanChart extends ChartWidget
{
    protected static ?string $heading = 'Pendaftaran per Bulan (Tahun ini)';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $year = now()->year;

        $rows = PendaftaranMagang::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $values = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create($year, $m)->translatedFormat('M');
            $values[] = $rows[$m]->total ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendaftaran',
                    'data'  => $values,
                    'backgroundColor' => 'rgba(220, 38, 38, 0.7)', // merah BPS vibes
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
