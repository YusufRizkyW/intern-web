<?php

namespace App\Filament\Resources\KuotaMagangResource\Pages;

use App\Filament\Resources\KuotaMagangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKuotaMagangs extends ListRecords
{
    protected static string $resource = KuotaMagangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
