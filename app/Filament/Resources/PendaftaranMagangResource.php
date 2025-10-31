<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftaranMagangResource\Pages;
use App\Filament\Resources\PendaftaranMagangResource\RelationManagers\BerkasPendaftaranRelationManager;
use App\Models\PendaftaranMagang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\RiwayatMagang;

class PendaftaranMagangResource extends Resource
{
    protected static ?string $model = PendaftaranMagang::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pendaftar Magang';
    protected static ?string $pluralLabel = 'Pendaftar Magang';
    protected static ?string $modelLabel = 'Pendaftar Magang';
    protected static ?string $navigationGroup = 'Magang';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Bagian Data Diri
            Forms\Components\Section::make('Data Pendaftar')
                ->schema([
                    Forms\Components\TextInput::make('nama_lengkap')
                        ->label('Nama')
                        ->required()
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
                ])->columns(2),

            // Bagian Periode Magang
            Forms\Components\Section::make('Periode Magang')
                ->schema([
                    Forms\Components\Select::make('tipe_periode')
                        ->options([
                            'durasi' => 'Durasi (x bulan)',
                            'tanggal' => 'Tanggal mulai & selesai',
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
                ])->columns(2),

            // Bagian Status Verifikasi (admin bisa ubah ini!)
            Forms\Components\Section::make('Status Verifikasi')
                ->schema([
                    Forms\Components\Select::make('status_verifikasi')
                        ->label('Status Verifikasi')
                        ->options([
                            'pending'  => 'Pending',
                            'revisi'   => 'Perlu Revisi',
                            'diterima' => 'Diterima',
                            'ditolak'  => 'Ditolak',
                        ])
                        ->required()
                        ->native(false),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status_verifikasi')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'revisi',
                        'success' => 'diterima',
                        'danger'  => 'ditolak',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipe_periode')
                    ->label('Tipe Periode')
                    ->formatStateUsing(fn ($state) => $state === 'durasi' ? 'Durasi' : 'Tanggal')
                    ->badge()
                    ->colors([
                        'gray' => 'durasi',
                        'gray' => 'tanggal',
                    ])
                    ->toggleable(),

                Tables\Columns\TextColumn::make('durasi_bulan')
                    ->label('Durasi (bulan)')
                    ->suffix(' bln')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(), // admin bisa ubah status_verifikasi di sini


                // ✅ ACTION BARU: Jadikan Riwayat
                Tables\Actions\Action::make('jadikan_riwayat')
                    ->label('Jadikan Riwayat')
                    ->icon('heroicon-o-archive-box')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (PendaftaranMagang $record) => $record->status_verifikasi === 'diterima')
                    ->action(function (PendaftaranMagang $record): void {
                        // 1. buat entri di tabel riwayat
                        RiwayatMagang::create([
                            'user_id'        => $record->user_id,
                            'nama_lengkap'   => $record->nama_lengkap,
                            'nim'            => $record->nim,
                            'email'          => $record->email,
                            'instansi'       => 'BPS Gresik', // kamu bisa ganti jadi field di pendaftaran kalau ada
                            'posisi'         => null, // kalau di pendaftaran ada kolom divisi, isi dari situ
                            'tanggal_mulai'  => $record->tanggal_mulai,
                            'tanggal_selesai'=> $record->tanggal_selesai,
                            'catatan_admin'  => null,
                            'file_sertifikat'=> null,
                        ]);

                        // 2. update status pendaftar jadi 'selesai'
                        $record->update([
                            'status_verifikasi' => 'selesai',
                        ]);
                    })
                    ->after(function () {
                        // optional: kalau mau notifikasi filament
                        \Filament\Notifications\Notification::make()
                            ->title('Dipindahkan ke Riwayat')
                            ->body('Data pendaftar berhasil dipindahkan ke riwayat magang.')
                            ->success()
                            ->send();
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // opsional, bisa dihapus kalau gak mau admin hapus data
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BerkasPendaftaranRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftaranMagangs::route('/'),
            // 'view'  => Pages\ViewPendaftaranMagang::route('/{record}'),
            'edit'  => Pages\EditPendaftaranMagang::route('/{record}/edit'),
        ];
    }
}
