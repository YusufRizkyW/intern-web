<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MagangDiterimaResource\Pages;
use App\Filament\Resources\PendaftaranMagangResource\RelationManagers\MembersRelationManager;
use App\Filament\Resources\PendaftaranMagangResource\RelationManagers\StatusLogsRelationManager;
use App\Models\PendaftaranMagang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class MagangDiterimaResource extends Resource
{
    protected static ?string $model = PendaftaranMagang::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Diterima';
    protected static ?string $navigationGroup = 'Magang';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // 1. info akun
            Forms\Components\Section::make('Akun Pengaju')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('User (yang login)')
                        ->relationship('user', 'name')
                        ->disabled(), // ini memang gak boleh diubah dari admin
                ]),

            // 2. data pendaftar (bisa individu / ketua tim)
            Forms\Components\Section::make('Data Pendaftar')
                ->schema([
                    Forms\Components\TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap / Ketua')
                        ->disabled(),

                    Forms\Components\TextInput::make('agency')
                        ->label('Instansi / Sekolah / Kampus')
                        ->disabled(),

                    Forms\Components\TextInput::make('nim')
                        ->label('NIM / NIS')
                        ->disabled(),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->disabled(),

                    Forms\Components\TextInput::make('no_hp')
                        ->label('No HP / WA')
                        ->disabled(),

                    Forms\Components\TextInput::make('tipe_pendaftaran')
                        ->label('Tipe Pendaftaran')
                        ->formatStateUsing(fn ($state) => $state === 'tim' ? 'Tim / Rombongan' : 'Individu')
                        ->disabled(),
                ])
                ->columns(2),

            // 3. periode
            Forms\Components\Section::make('Periode Magang')
                ->schema([
                    Forms\Components\Select::make('tipe_periode')
                        ->label('Mode Periode')
                        ->options([
                            'durasi'  => 'Durasi (x bulan)',
                            'tanggal' => 'Tanggal Mulai & Selesai',
                        ])
                        ->disabled(),

                    Forms\Components\TextInput::make('durasi_bulan')
                        ->label('Durasi (bulan)')
                        ->disabled(),

                    Forms\Components\DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->disabled(),

                    Forms\Components\DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Selesai')
                        ->disabled(),
                ])
                ->columns(2),

                
            // 4. link drive
            Forms\Components\Section::make('Link Berkas (Google Drive)')
                ->schema([
                    Forms\Components\TextInput::make('link_drive')
                        ->label('Link Drive')
                        ->disabled()
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('openLink')
                                ->icon('heroicon-o-link')
                                ->url(fn ($record) => $record?->link_drive, shouldOpenInNewTab: true)
                                ->tooltip('Buka di tab baru')
                        ),
                ]),

            // 5. catatan admin (boleh diubah)
            Forms\Components\Section::make('Catatan Admin')
                ->schema([
                    Forms\Components\Textarea::make('catatan_admin')
                        ->label('Catatan untuk peserta')
                        ->rows(3)
                        ->helperText('Catatan ini ditampilkan ke user di halaman status.'),
                ]),

            // 6. status verifikasi (ini yang paling penting)
            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Select::make('status_verifikasi')
                        ->label('Status Verifikasi')
                        ->options([
                            // 'pending'  => 'Pending / Menunggu',
                            // 'revisi'   => 'Perlu Revisi',
                            'diterima' => 'Diterima',
                            'ditolak'  => 'Ditolak',
                            // nanti kalau mau lanjut pipeline bisa tambahin:
                            'aktif'    => 'Aktif',
                            // 'selesai'  => 'Selesai',
                            'batal'    => 'Dibatalkan',
                            // 'arsip'    => 'Diarsipkan',
                        ])
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->rowIndex()
                    ->label('No')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Akun Pengaju')
                    ->toggleable()
                    ->searchable(),

                // Tables\Columns\TextColumn::make('nama_lengkap')
                //     ->label('Nama / Ketua')
                //     ->searchable()
                //     ->sortable(),

                Tables\Columns\TextColumn::make('agency')
                    ->label('Instansi')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('tipe_pendaftaran')
                    ->label('Tipe')
                    ->formatStateUsing(fn ($state) => $state === 'tim' ? 'Tim' : 'Individu')
                    ->colors([
                        'primary' => 'individu',
                        'info'    => 'tim',
                    ]),

                Tables\Columns\BadgeColumn::make('status_verifikasi')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'revisi',
                        'success' => 'diterima',
                        'danger'  => 'ditolak',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('link_drive')
                    ->label('Drive')
                    ->url(fn ($record) => $record->link_drive, shouldOpenInNewTab: true)
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->link_drive),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_verifikasi')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'revisi'   => 'Revisi',
                        'diterima' => 'Diterima',
                        'ditolak'  => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('tipe_pendaftaran')
                    ->label('Tipe')
                    ->options([
                        'individu' => 'Individu',
                        'tim'      => 'Tim',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MembersRelationManager::class,
            StatusLogsRelationManager::class,
            // kalau nanti kamu bikin tabel berkas, tinggal tambah di sini
            // BerkasPendaftaranRelationManager::class,
        ];
    }

    /**
     * Hanya tampilkan yang statusnya DITERIMA
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status_verifikasi', 'diterima');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMagangDiterimas::route('/'),
            'edit' => Pages\EditMagangDiterima::route('/{record}/edit'),
        ];
    }
}
