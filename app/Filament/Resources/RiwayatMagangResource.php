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
                            ->disabled()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        $set('nama_lengkap', $user->name);
                                        $set('email', $user->email);
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('nama_lengkap')
                            ->disabled(),

                        Forms\Components\TextInput::make('nim')
                            ->disabled(),

                        Forms\Components\TextInput::make('email')
                            ->disabled(),

                        Forms\Components\TextInput::make('no_hp')
                            ->label('No. HP / WA')
                            ->disabled(),

                    ])->columns(2),

                Forms\Components\Section::make('Detail Magang')
                    ->schema([
                        Forms\Components\TextInput::make('agency')
                            ->label('Instansi')
                            ->disabled(),

                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->disabled(),

                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->disabled(),

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

                // Tables\Columns\TextColumn::make('created_at')
                //     ->since()
                //     ->label('Diinput'),
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
