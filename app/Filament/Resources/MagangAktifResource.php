<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MagangAktifResource\Pages;
use App\Models\PendaftaranMagang;
use App\Filament\Resources\PendaftaranMagangResource\RelationManagers\BerkasPendaftaranRelationManager;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MagangAktifResource extends Resource
{
    protected static ?string $model = PendaftaranMagang::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationLabel = 'Aktif';
    protected static ?string $navigationGroup = 'Magang';
    protected static ?int $navigationSort = 4;

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Akun pendaftar')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('User')
                        ->relationship('user', 'name')
                        ->disabled(),
                ]),

            Forms\Components\Section::make('Data pendaftar')
                ->schema([
                    Forms\Components\TextInput::make('nama_lengkap')
                        ->disabled(),

                    Forms\Components\TextInput::make('agency')
                        ->label('Instansi')
                        ->disabled(),

                    Forms\Components\TextInput::make('email')
                        ->disabled(),

                    Forms\Components\DatePicker::make('tanggal_mulai')
                        ->disabled(),

                    Forms\Components\DatePicker::make('tanggal_selesai')
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

            Forms\Components\Section::make('Status Verifikasi')
                ->schema([
                    Forms\Components\Select::make('status_verifikasi')
                        ->label('Update Status Verifikasi')
                        ->options([
                            'diterima' => 'Diterima',
                            'selesai' => 'Selesai',
                            'batal' => 'Batal',
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
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('agency')
                    ->label('Instansi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status_verifikasi')
                    ->colors([
                        'success' => 'aktif',
                    ])
                    ->label('Status'),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date()
                    ->label('Mulai'),
                
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->date()
                    ->label('Selesai'),


            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    /**
     * Hanya yang benar2 aktif
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status_verifikasi', 'aktif');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMagangAktifs::route('/'),
            'edit' => Pages\EditMagangAktif::route('/{record}/edit'),
        ];
    }
}
