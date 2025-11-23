<?php

namespace App\Filament\Resources\PendaftaranMagangResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;

class MembersRelationManager extends RelationManager
{
    // harus sama dengan nama relasi di model PendaftaranMagang
    protected static string $relationship = 'members';

    protected static ?string $title = 'Anggota Tim';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_anggota')
                ->label('Nama Anggota')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('nim_anggota')
                ->label('NIM / NIS')
                ->maxLength(50),

            Forms\Components\TextInput::make('email_anggota')
                ->label('Email')
                ->email()
                ->maxLength(255),

            Forms\Components\TextInput::make('no_hp_anggota')
                ->label('No HP / WA')
                ->maxLength(30),

            Forms\Components\Toggle::make('is_ketua')
                ->label('Ketua Tim')
                ->inline(false),
        ])->columns(2);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->rowIndex()
                    ->label('No')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('nama_anggota')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('agency_anggota')
                    ->label('Instansi'),

                Tables\Columns\TextColumn::make('nim_anggota')
                    ->label('NIM / NIS'),

                Tables\Columns\TextColumn::make('email_anggota')
                    ->label('Email'),

                Tables\Columns\TextColumn::make('no_hp_anggota')
                    ->label('No HP'),

                Tables\Columns\IconColumn::make('is_ketua')
                    ->label('Ketua')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
