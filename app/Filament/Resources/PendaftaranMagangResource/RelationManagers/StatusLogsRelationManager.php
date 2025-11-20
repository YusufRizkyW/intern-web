<?php

namespace App\Filament\Resources\PendaftaranMagangResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';
    protected static ?string $title = 'Riwayat Perubahan Status';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status_baru')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'revisi'   => 'Revisi',
                        'diterima' => 'Diterima',
                        'ditolak'  => 'Ditolak',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status_baru')
            ->columns([
                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Admin')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status_lama')
                    ->label('Status Lama')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'revisi' => 'info',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        'aktif' => 'success',
                        default => 'gray',
                    })
                    ->placeholder('(Status Awal)'),
                    
                Tables\Columns\TextColumn::make('status_baru')
                    ->label('Status Baru')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'revisi' => 'info',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        'aktif' => 'success',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('catatan')
                    ->label('Catatan')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tidak perlu create action karena otomatis dibuat saat status berubah
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tidak perlu bulk actions
            ])
            ->defaultSort('created_at', 'desc');
    }
}
