<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatMagangResource\Pages;
use App\Models\RiwayatMagang;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RiwayatMagangResource extends Resource
{
    protected static ?string $model = RiwayatMagang::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Riwayat';
    protected static ?string $navigationGroup = 'Magang';
    protected static ?int $navigationSort = 5;
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Akun pendaftar
            Forms\Components\Section::make('Akun pendaftar')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('User')
                        ->relationship('user', 'name')
                        ->disabled(),
                ]),

            // Data pendaftar utama
            Forms\Components\Section::make('Data Pendaftar')
                ->schema([
                    Forms\Components\TextInput::make('nama_lengkap')
                        ->label('Nama')
                        ->disabled(),

                    Forms\Components\TextInput::make('agency')
                        ->label('Instansi')
                        ->disabled(),

                    Forms\Components\TextInput::make('nim')
                        ->label('NIM')
                        ->disabled(),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->disabled(),

                    Forms\Components\TextInput::make('no_hp')
                        ->label('No. HP / WA')
                        ->disabled(),
                ])
                ->columns(2),

            // 🔹 Tipe pendaftaran & anggota tim (READ ONLY)
            Forms\Components\Section::make('Tipe Pendaftaran & Anggota Tim')
                ->schema([
                    Forms\Components\Placeholder::make('tipe_pendaftaran_info')
                        ->label('Tipe Pendaftaran')
                        ->content(function (\App\Models\RiwayatMagang $record) {
                            $tipe = $record->pendaftaranMagang?->tipe_pendaftaran;

                            return match ($tipe) {
                                'tim'      => 'Tim / Rombongan',
                                'individu' => 'Individu (1 orang)',
                                default    => '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('anggota_tim_info')
                        ->label('Anggota Tim')
                        ->content(function (\App\Models\RiwayatMagang $record) {
                            $pendaftaran = $record->pendaftaranMagang;
                            if (! $pendaftaran) {
                                return 'Data pendaftaran asal tidak ditemukan.';
                            }

                            $members = $pendaftaran->members;

                            if (! $members || $members->isEmpty()) {
                                return 'Tidak ada anggota tim (kemungkinan pendaftaran individu).';
                            }

                            return $members
                                ->map(function ($m) {
                                    $text = $m->nama_anggota;
                                    if ($m->nim_anggota) {
                                        $text .= ' (' . $m->nim_anggota . ')';
                                    }
                                    if ($m->is_ketua ?? false) {
                                        $text .= ' – Ketua';
                                    }
                                    return $text;
                                })
                                ->implode("\n");
                        })
                        ->extraAttributes([
                            'style' => 'white-space: pre-line;', // supaya newline kebaca
                        ]),
                ])
                ->columns(1),

            // Periode
            Forms\Components\Section::make('Periode Magang')
                ->schema([
                    Forms\Components\DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->disabled(),

                    Forms\Components\DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Selesai')
                        ->disabled(),
                ])
                ->columns(2),

            // Link drive + status akhir
            Forms\Components\Section::make('Berkas')
                ->schema([
                    Forms\Components\TextInput::make('link_drive')
                        ->label('Link Google Drive')
                        ->disabled()
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('openLink')
                                ->icon('heroicon-o-link')
                                ->url(fn ($record) => $record?->link_drive, shouldOpenInNewTab: true)
                                ->tooltip('Buka Link Drive')
                        ),

                    Forms\Components\Select::make('status_verifikasi')
                        ->label('Status Akhir')
                        ->options([
                            'selesai' => 'Selesai',
                            'batal'   => 'Batal',
                            'arsip'   => 'Arsip',
                        ])
                        ->disabled(),
                ])
                ->columns(2),

            // Catatan & sertifikat (masih bisa diubah admin)
            Forms\Components\Section::make('Catatan & Sertifikat')
                ->schema([
                    Forms\Components\Textarea::make('catatan_admin')
                        ->label('Catatan Admin')
                        ->rows(3)
                        ->helperText('Catatan ini akan terlihat oleh user')
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('file_sertifikat')
                        ->label('Sertifikat (PDF)')
                        ->disk('public')
                        ->directory('sertifikat_magang')
                        ->acceptedFileTypes(['application/pdf'])
                        ->downloadable()
                        ->openable(),
                ]),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                ->rowIndex()        // otomatis 1,2,3...
                ->label('No')
                ->alignCenter()
                ->sortable(false)
                ->searchable(false),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Akun')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('agency')
                    ->label('Instansi')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status_verifikasi')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'aktif' => 'Aktif',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        'arsip' => 'Arsip',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date()
                    ->label('Mulai'),

                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->date()
                    ->label('Selesai'),

            ])
            
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatMagangs::route('/'),
            'create' => Pages\CreateRiwayatMagang::route('/create'),
            'edit' => Pages\EditRiwayatMagang::route('/{record}/edit'),
        ];
    }

}
