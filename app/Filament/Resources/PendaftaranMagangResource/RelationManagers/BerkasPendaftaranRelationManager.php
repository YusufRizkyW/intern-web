<?php

namespace App\Filament\Resources\PendaftaranMagangResource\RelationManagers;

use App\Models\BerkasPendaftaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class BerkasPendaftaranRelationManager extends RelationManager
{
    protected static string $relationship = 'berkas';
    protected static ?string $title = 'Berkas Dokumen';

    public function form(Form $form): Form
    {
        return $form->schema([
            // Jenis berkas (CV / Surat Pengantar / KTM)
            Forms\Components\TextInput::make('jenis_berkas')
                ->label('Jenis Berkas')
                ->disabled(),

            // File PDF yang diupload user
            Forms\Components\FileUpload::make('path_file')
                ->label('File PDF')
                ->directory('berkas_pendaftaran')
                ->disk('public')
                ->acceptedFileTypes(['application/pdf'])
                ->openable()     // tombol "Open" (buka di tab baru)
                ->downloadable() // tombol "Download"
                ->deletable(false)
                ->disabled(),    // admin tidak boleh ganti file

            Forms\Components\Select::make('valid')
                ->label('Status Dokumen')
                ->options([
                    'pending' => 'Pending',
                    'valid'   => 'Valid',
                    'invalid' => 'Tidak Valid',
                ])
                ->required(),

            Forms\Components\Textarea::make('catatan_admin')
                ->label('Catatan Admin (jika tidak valid)')
                ->rows(3)
                ->placeholder('Contoh: Mohon upload ulang, stempel kampus kurang jelas'),
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jenis_berkas')
                    ->label('Jenis'),

                Tables\Columns\TextColumn::make('valid')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'valid',
                        'danger'  => 'invalid',
                    ]),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->since(), // contoh: "5 minutes ago"
            ])
            ->actions([
                // Admin bisa edit status 'valid' & 'catatan_admin'
                Tables\Actions\EditAction::make(),

                // Tombol custom untuk lihat PDF full size
                Tables\Actions\Action::make('lihat')
                    ->label('Lihat PDF')
                    ->url(function (BerkasPendaftaran $record) {
                        // route custom untuk preview file
                        return route('admin.berkas.view', $record->id);
                    })
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                // Jangan izinkan tambah/hapus dari sini.
                // Kalau kamu mau larang admin nambah berkas baru:
                // cukup jangan taruh CreateAction
            ]);
    }
}
