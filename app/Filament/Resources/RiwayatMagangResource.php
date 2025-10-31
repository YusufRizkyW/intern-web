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
    protected static ?string $navigationLabel = 'Riwayat Magang';
    protected static ?string $navigationGroup = 'Magang';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Peserta')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        $set('nama_lengkap', $user->name);
                                        $set('email', $user->email);
                                    }
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('nama_lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('nim')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Magang')
                    ->schema([
                        Forms\Components\TextInput::make('instansi')
                            ->default('BPS Gresik')
                            ->maxLength(255),

                        // Forms\Components\TextInput::make('posisi')
                        //     ->label('Divisi / Penempatan')
                        //     ->maxLength(255),

                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai'),

                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai'),
                    ])->columns(2),

                Forms\Components\Section::make('Catatan & Sertifikat')
                    ->schema([
                        Forms\Components\Textarea::make('catatan_admin')
                            ->rows(3),

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
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('instansi')
                    ->label('Instansi')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date()
                    ->label('Mulai'),

                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->date()
                    ->label('Selesai'),

                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->label('Diinput'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
