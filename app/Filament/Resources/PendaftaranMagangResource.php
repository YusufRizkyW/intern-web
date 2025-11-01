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
use Illuminate\Database\Eloquent\Builder;


class PendaftaranMagangResource extends Resource
{
    protected static ?string $model = PendaftaranMagang::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pendaftar';
    protected static ?string $navigationGroup = 'Magang';
    protected static ?int $navigationSort = 1;



    public static function form(Form $form): Form
    {
        return $form->schema([
            // Bagian Data Diri
            Forms\Components\Section::make('Akun pendaftar')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('User')
                        ->relationship('user', 'name')
                        ->disabled(),
                ]),
            Forms\Components\Section::make('Data Pendaftar')
                ->schema([
                    Forms\Components\TextInput::make('nama_lengkap')
                        ->label('Nama')
                        ->required()
                        ->disabled(),

                    Forms\Components\TextInput::make('agency')
                        ->label('Instansi')
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

            Forms\Components\Section::make('Link Berkas')
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
                ])->columns(1),
            
            Forms\Components\Section::make('Catatan Admin')
                ->schema([
                    Forms\Components\Textarea::make('catatan_admin')
                        ->label('Catatan Admin')
                        ->rows(3)
                        ->helperText('Catatan ini akan terlihat oleh user di halaman status.')
                        ->columnSpanFull(),
                ])->columns(1),


            // Bagian Status Verifikasi (admin bisa ubah ini!)
            Forms\Components\Section::make('Status Verifikasi')
                ->schema([
                    Forms\Components\Select::make('status_verifikasi')
                        ->label('Update Status Verifikasi')
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
                    ->searchable()
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('link_drive')
                    ->label('Link Drive')
                    ->url(fn ($record) => $record->link_drive, shouldOpenInNewTab: true)
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->link_drive),


                Tables\Columns\TextColumn::make('created_at')
                    ->label('Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ]);
        }
        


     public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('user')
            ->whereIn('status_verifikasi', ['pending', 'revisi', 'ditolak']);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftaranMagangs::route('/'),
            'edit'  => Pages\EditPendaftaranMagang::route('/{record}/edit'),
        ];
    }
}
