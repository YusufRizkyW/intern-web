<?php

namespace App\Filament\Resources\PendaftaranMagangResource\Pages;

use App\Filament\Resources\PendaftaranMagangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPendaftaranMagangs extends ListRecords
{
    protected static string $resource = PendaftaranMagangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('💡 Tip: Buat kuota magang sebelum menerima pendaftar')
                ->disabled()
                ->view('components.alert-filament')
                ->viewData([
                    'message' => '💡 Tip: Pastikan membuat kuota magang sebelum menerima pendaftar'
                ])
        ];
    }
}
