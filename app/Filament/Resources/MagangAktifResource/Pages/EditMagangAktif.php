<?php

namespace App\Filament\Resources\MagangAktifResource\Pages;

use App\Filament\Resources\MagangAktifResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMagangAktif extends EditRecord
{
    protected static string $resource = MagangAktifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
