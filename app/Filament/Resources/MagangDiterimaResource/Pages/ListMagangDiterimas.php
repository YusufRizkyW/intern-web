<?php

namespace App\Filament\Resources\MagangDiterimaResource\Pages;

use App\Filament\Resources\MagangDiterimaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMagangDiterimas extends ListRecords
{
    protected static string $resource = MagangDiterimaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('💡 Tip: Buat kuota magang sebelum menerima pendaftar')
                ->disabled()
                ->view('components.info-filament')
                ->viewData([
                    'message' => '💡 Tip: Seluruh pendaftar yang diterima akan berada disini'
                ]),
        ];
    }
}
