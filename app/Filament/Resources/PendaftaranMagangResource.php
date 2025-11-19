<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftaranMagangResource\Pages;
use App\Filament\Resources\PendaftaranMagangResource\RelationManagers\MembersRelationManager;
use App\Filament\Resources\PendaftaranMagangResource\RelationManagers\StatuslogsRelationManager;
use App\Models\PendaftaranMagang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class PendaftaranMagangResource extends Resource
{
    protected static ?string $model = PendaftaranMagang::class;

    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pendaftar';
    protected static ?string $navigationGroup = 'Magang';
    protected static ?int    $navigationSort  = 2;

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
                            'pending'  => 'Pending / Menunggu',
                            'revisi'   => 'Perlu Revisi',
                            'diterima' => 'Diterima',
                            'ditolak'  => 'Ditolak',
                            // 'aktif'    => 'Sedang Magang',
                            // 'selesai'  => 'Selesai',
                            // 'batal'    => 'Dibatalkan',
                            // 'arsip'    => 'Diarsipkan',
                        ])
                        ->required()
                        ->reactive()
                        ->helperText(function (Forms\Get $get, ?\Illuminate\Database\Eloquent\Model $record) {
                            if (!$record) return null;
                            
                            $statusBaru = $get('status_verifikasi');
                            $statusLama = $record->getOriginal('status_verifikasi');
                            
                            // Jika akan diubah ke 'diterima', cek kuota
                            if ($statusBaru === 'diterima' && $statusLama !== 'diterima') {
                                $validation = $record->canBeApprovedDetailed();
                                
                                if (!$validation['can_approve']) {
                                    return '⚠️ ' . $validation['message'];
                                } else {
                                    return '✅ ' . $validation['message'];
                                }
                            }
                            
                            return null;
                        })
                        ->rules([
                            function () {
                                return function (string $attribute, $value, \Closure $fail) {
                                    if ($value === 'diterima') {
                                        $record = request()->route('record');
                                        if ($record && $record->getOriginal('status_verifikasi') !== 'diterima') {
                                            $validation = $record->canBeApprovedDetailed();
                                            
                                            if (!$validation['can_approve']) {
                                                $fail($validation['message']);
                                            }
                                        }
                                    }
                                };
                            }
                        ]),
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
                
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PendaftaranMagang $record): bool => $record->status_verifikasi === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pendaftaran')
                    ->modalDescription(function (PendaftaranMagang $record) {
                        $validation = $record->canBeApprovedDetailed();
                        return $validation['message'];
                    })
                    ->modalSubmitActionLabel('Ya, Setujui')
                    ->action(function (PendaftaranMagang $record) {
                        $validation = $record->canBeApprovedDetailed();
                        
                        if (!$validation['can_approve']) {
                            Notification::make()
                                ->title('Gagal Menyetujui!')
                                ->body($validation['message'])
                                ->danger()
                                ->send();
                            return;
                        }
                        
                        $record->update(['status_verifikasi' => 'diterima']);
                        
                        Notification::make()
                            ->title('Berhasil!')
                            ->body('Pendaftaran berhasil disetujui')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (PendaftaranMagang $record): bool => in_array($record->status_verifikasi, ['pending', 'diterima']))
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pendaftaran')
                    ->modalDescription('Apakah Anda yakin ingin menolak pendaftaran ini?')
                    ->modalSubmitActionLabel('Ya, Tolak')
                    ->action(function (PendaftaranMagang $record) {
                        $record->update(['status_verifikasi' => 'ditolak']);
                        
                        Notification::make()
                            ->title('Berhasil!')
                            ->body('Pendaftaran berhasil ditolak')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data, $record): array {
                        // Jika status diubah ke 'diterima', validasi kuota
                        if ($data['status_verifikasi'] === 'diterima' && 
                            $record->status_verifikasi !== 'diterima') {
                            
                            $validation = $record->canBeApprovedDetailed();
                            
                            if (!$validation['can_approve']) {
                                Notification::make()
                                    ->title('Kuota Tidak Cukup!')
                                    ->body($validation['message'])
                                    ->danger()
                                    ->send();
                                
                                // Reset status ke semula
                                $data['status_verifikasi'] = $record->status_verifikasi;
                            }
                        }
                        
                        return $data;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * Tampilkan semua pendaftaran untuk pengelolaan admin
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('user')
            ->whereIn('status_verifikasi', [
                'pending',
                'revisi',
                'diterima',
                'aktif',
                // 'ditolak', 'selesai', 'batal', 'arsip' sudah pindah ke riwayat
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftaranMagangs::route('/'),
            // 'view'  => Pages\ViewPendaftaranMagang::route('/{record}'),
            'edit'  => Pages\EditPendaftaranMagang::route('/{record}/edit'),
        ];
    }
}
