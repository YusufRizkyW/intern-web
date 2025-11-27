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
            //Actions\CreateAction::make(),
        ];
    }
}
