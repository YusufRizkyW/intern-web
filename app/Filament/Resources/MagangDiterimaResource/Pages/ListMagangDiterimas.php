<?php

namespace App\Filament\Resources\MagangDiterimaResource\Pages;

use App\Filament\Resources\MagangDiterimaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMagangDiterimas extends ListRecords
{
    protected static string $resource = MagangDiterimaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
