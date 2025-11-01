<?php

namespace App\Filament\Resources\MagangDiterimaResource\Pages;

use App\Filament\Resources\MagangDiterimaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMagangDiterima extends EditRecord
{
    protected static string $resource = MagangDiterimaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
