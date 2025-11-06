<?php

namespace App\Filament\Widgets;

use App\Models\PendaftaranMagang;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class RecentPendaftarTable extends BaseWidget
{
    protected static ?string $heading = 'Pendaftar Terbaru';

    protected function getTableQuery(): Builder|Relation|null
    {
        return PendaftaranMagang::query()
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nama_lengkap')
                ->label('Nama')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('agency')
                ->label('Instansi')
                ->sortable(),

            Tables\Columns\TextColumn::make('tipe_pendaftaran')
                ->label('Tipe')
                ->formatStateUsing(fn ($state) => $state === 'tim' ? 'Tim' : 'Individu')
                ->badge(),

            Tables\Columns\TextColumn::make('status_verifikasi')
                ->label('Status')
                ->badge()
                ->colors([
                    'warning' => 'pending',
                    'info'    => 'revisi',
                    'success' => 'diterima',
                    'danger'  => 'ditolak',
                ]),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Daftar')
                ->dateTime('d M Y H:i')
                ->sortable(),
        ];
    }
}
