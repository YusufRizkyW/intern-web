<?php

namespace App\Filament\Resources\KuotaMagangResource\Pages;

use App\Filament\Resources\KuotaMagangResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListKuotaMagangs extends ListRecords
{
    protected static string $resource = KuotaMagangResource::class;

    protected function getHeaderActions(): array
    {
        return [

            // Info message sebagai disabled action
            Actions\Action::make('info')
                ->label('💡 Tip: Buat kuota magang sebelum menerima pendaftar')
                ->disabled()
                ->view('components.info-filament')
                ->viewData([
                    'message' => '💡 Tip: Buat kuota magang sebelum menerima pendaftar'
                ]),
                
            // Create button  
            Actions\CreateAction::make()
                ->label('Tambah Kuota')
                ->icon('heroicon-o-plus')
                ->color('success'),
        ];
    }
}
