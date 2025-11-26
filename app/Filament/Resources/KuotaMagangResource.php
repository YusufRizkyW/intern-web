<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KuotaMagangResource\Pages;
use App\Models\KuotaMagang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class KuotaMagangResource extends Resource
{
    protected static ?string $model = KuotaMagang::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Kuota Magang';
    protected static ?string $navigationGroup = 'Manajemen Magang';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Periode Kuota')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('tahun')
                                ->label('Tahun')
                                ->options(function () {
                                    $currentYear = date('Y');
                                    $years = [];
                                    for ($i = $currentYear - 1; $i <= $currentYear + 2; $i++) {
                                        $years[$i] = $i;
                                    }
                                    return $years;
                                })
                                ->default(date('Y'))
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set) => $set('_check_duplicate', time()))
                                ->rules([
                                    'required',
                                    function () {
                                        return function (string $attribute, $value, \Closure $fail) {
                                            $tahun = $value;
                                            $bulan = request()->input('data.bulan');
                                            $currentId = request()->route('record'); // ID saat edit

                                            if ($tahun && $bulan) {
                                                $exists = KuotaMagang::where('tahun', $tahun)
                                                    ->where('bulan', $bulan)
                                                    ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                                                    ->exists();

                                                if ($exists) {
                                                    $namaBulan = KuotaMagang::getNamaBulan($bulan);
                                                    $fail("Kuota untuk periode {$namaBulan} {$tahun} sudah ada!");
                                                }
                                            }
                                        };
                                    }
                                ]),

                            Forms\Components\Select::make('bulan')
                                ->label('Bulan')
                                ->options([
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                    4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                    10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                ])
                                ->default(date('n'))
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set) => $set('_check_duplicate', time()))
                                ->rules([
                                    'required',
                                    function () {
                                        return function (string $attribute, $value, \Closure $fail) {
                                            $bulan = $value;
                                            $tahun = request()->input('data.tahun');
                                            $currentId = request()->route('record'); // ID saat edit

                                            if ($tahun && $bulan) {
                                                $exists = KuotaMagang::where('tahun', $tahun)
                                                    ->where('bulan', $bulan)
                                                    ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                                                    ->exists();

                                                if ($exists) {
                                                    $namaBulan = KuotaMagang::getNamaBulan($bulan);
                                                    $fail("Kuota untuk periode {$namaBulan} {$tahun} sudah ada!");
                                                }
                                            }
                                        };
                                    }
                                ]),
                        ]),
                ]),

            Forms\Components\Section::make('Pengaturan Kuota')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('kuota_maksimal')
                                ->label('Kuota Maksimal')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->helperText('Jumlah maksimal peserta magang untuk periode ini')
                                ->rules([
                                    'required',
                                    'numeric',
                                    'min:1',
                                    function () {
                                        return function (string $attribute, $value, \Closure $fail) {
                                            $kuotaTerisi = request()->input('data.kuota_terisi', 0);
                                            if ($value < $kuotaTerisi) {
                                                $fail("Kuota maksimal tidak boleh lebih kecil dari kuota terisi ({$kuotaTerisi} peserta)");
                                            }
                                        };
                                    }
                                ]),

                            Forms\Components\TextInput::make('kuota_terisi')
                                ->label('Kuota Terisi')
                                ->numeric()
                                ->default(0)
                                ->disabled()
                                ->helperText('Akan diupdate otomatis berdasarkan pendaftaran yang diterima'),
                        ]),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true)
                        ->helperText('Hanya kuota aktif yang akan digunakan untuk validasi'),

                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan')
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText('Catatan tambahan tentang kuota periode ini'),
                ]),

            Forms\Components\Hidden::make('_check_duplicate')
                ->rules([
                    function () {
                        return function (string $attribute, $value, \Closure $fail) {
                            $tahun = request()->input('data.tahun');
                            $bulan = request()->input('data.bulan');
                            $currentId = request()->route('record'); // ID saat edit

                            if ($tahun && $bulan) {
                                $exists = KuotaMagang::where('tahun', $tahun)
                                    ->where('bulan', $bulan)
                                    ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                                    ->exists();

                                if ($exists) {
                                    $namaBulan = KuotaMagang::getNamaBulan($bulan);
                                    
                                    Notification::make()
                                        ->title('Gagal Menyimpan!')
                                        ->body("Kuota untuk periode {$namaBulan} {$tahun} sudah ada. Silakan pilih periode yang berbeda.")
                                        ->danger()
                                        ->persistent()
                                        ->send();

                                    $fail("Kuota untuk periode {$namaBulan} {$tahun} sudah ada!");
                                }
                            }
                        };
                    }
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->sortable(['tahun', 'bulan'])
                    ->searchable(),

                Tables\Columns\TextColumn::make('kuota_maksimal')
                    ->label('Kuota Maksimal')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kuota_terisi')
                    ->label('Kuota Terisi')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sisa_kuota')
                    ->label('Sisa Kuota')
                    ->getStateUsing(fn ($record) => $record->sisa_kuota)
                    ->color(fn ($state) => $state <= 5 ? 'danger' : ($state <= 10 ? 'warning' : 'success'))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('persentase_kuota')
                    ->label('Progress')
                    ->getStateUsing(function ($record) {
                        $persentase = $record->persentase_kuota;
                        return number_format($persentase, 1) . '%';
                    })
                    ->color(function ($record) {
                        $persentase = $record->persentase_kuota;
                        return $persentase >= 90 ? 'danger' : ($persentase >= 75 ? 'warning' : 'success');
                    })
                    ->badge()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tahun', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tahun')
                    ->options(function () {
                        return KuotaMagang::distinct()
                            ->pluck('tahun', 'tahun')
                            ->sort()
                            ->toArray();
                    }),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                ]);
            // ->actions([s
            //     Tables\Actions\EditAction::make(),
            //     Tables\Actions\DeleteAction::make()
            //         ->before(function ($record) {
            //             // Cek apakah ada kuota terisi sebelum delete
            //             if ($record->kuota_terisi > 0) {
            //                 Notification::make()
            //                     ->title('Tidak dapat menghapus!')
            //                     ->body("Masih ada {$record->kuota_terisi} peserta terdaftar untuk periode ini.")
            //                     ->danger()
            //                     ->send();
                                
            //                 return false; // Cancel delete
            //             }
            //         }),
            // ])
            // ->bulkActions([
            //     // Tables\Actions\BulkActionGroup::make([
            //     //     Tables\Actions\DeleteBulkAction::make()
            //     //         ->before(function ($records) {
            //     //             foreach ($records as $record) {
            //     //                 if ($record->kuota_terisi > 0) {
            //     //                     Notification::make()
            //     //                         ->title('Tidak dapat menghapus!')
            //     //                         ->body("Periode {$record->periode} masih memiliki {$record->kuota_terisi} peserta terdaftar.")
            //     //                         ->danger()
            //     //                         ->send();
                                        
            //     //                     return false; // Cancel bulk delete
            //     //                 }
            //     //             }
            //     //         }),
            //     // ]),
            // ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKuotaMagangs::route('/'),
            'create' => Pages\CreateKuotaMagang::route('/create'),
            'edit' => Pages\EditKuotaMagang::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        $currentKuota = KuotaMagang::getKuotaForPeriode($currentYear, $currentMonth);
        
        if (!$currentKuota) {
            return 'No Quota';
        }

        if ($currentKuota->sisa_kuota <= 5) {
            return $currentKuota->sisa_kuota . ' left!';
        }

        return $currentKuota->sisa_kuota . ' slots';
    }
}
