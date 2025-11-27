<?php

namespace App\Filament\Resources\MagangAktifResource\Pages;

use App\Filament\Resources\MagangAktifResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMagangAktifs extends ListRecords
{
    protected static string $resource = MagangAktifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('💡 Tip: Buat kuota magang sebelum menerima pendaftar')
                ->disabled()
                ->view('components.info-filament')
                ->viewData([
                    'message' => '💡 Tip: Seluruh pendaftar yang aktif akan berada disini'
                ]),
        ];
    }
}
