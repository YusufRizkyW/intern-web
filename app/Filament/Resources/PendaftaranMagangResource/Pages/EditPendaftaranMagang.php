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
            // Actions\DeleteAction::make()
            //     ->label('Hapus')
            //     ->icon('heroicon-o-trash')
            //     ->color('warning'),
            
            Actions\Action::make('info')
                ->label('💡 Tip: Buat kuota magang sebelum menerima pendaftar')
                ->disabled()
                ->view('components.alert-filament')
                ->viewData([
                    'message' => '💡 Tip: Pastikan membuat kuota magang sebelum menerima pendaftar'
                ])
        ];
    }
}
