<?php

namespace App\Filament\Resources\KuotaMagangResource\Pages;

use App\Filament\Resources\KuotaMagangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKuotaMagang extends EditRecord
{
    protected static string $resource = KuotaMagangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
