<?php

namespace App\Filament\Resources\RiwayatMagangResource\Pages;

use App\Filament\Resources\RiwayatMagangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatMagangs extends ListRecords
{
    protected static string $resource = RiwayatMagangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('Seluruh riwayat pendaftaran akan tersimpan disini')
                ->disabled()
                ->view('components.info-filament')
                ->viewData([
                    'message' => '💡 Tip: Seluruh riwayat pendaftaran akan tersimpan disini'
                ]),
        ];
    }
}
