<?php

namespace App\Filament\Resources\PendaftaranMagangResource\Pages;

use App\Filament\Resources\PendaftaranMagangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPendaftaranMagang extends EditRecord
{
    protected static string $resource = PendaftaranMagangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
