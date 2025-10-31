<?php

namespace App\Filament\Resources\RiwayatMagangResource\Pages;

use App\Filament\Resources\RiwayatMagangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatMagang extends EditRecord
{
    protected static string $resource = RiwayatMagangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
