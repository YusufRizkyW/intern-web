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
            // Actions\CreateAction::make(),
        ];
    }
}
