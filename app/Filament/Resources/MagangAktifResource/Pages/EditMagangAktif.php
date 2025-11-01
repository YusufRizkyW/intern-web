<?php

namespace App\Filament\Resources\MagangAktifResource\Pages;

use App\Filament\Resources\MagangAktifResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\RiwayatMagang;

class EditMagangAktif extends EditRecord
{
    protected static string $resource = MagangAktifResource::class;

    protected function afterSave(): void
    {
        $record = $this->record; // ini PendaftaranMagang

        // kalau status-nya sudah final → pindahkan ke riwayat
        if (in_array($record->status_verifikasi, ['selesai', 'batal', 'arsip'], true)) {

            // bikin entri riwayat (kalau belum ada untuk pendaftaran ini)
            RiwayatMagang::firstOrCreate(
                [
                    'pendaftaran_magang_id' => $record->id,
                ],
                [
                    'user_id'         => $record->user_id,
                    'nama_lengkap'    => $record->nama_lengkap,
                    'agency'          => $record->agency,
                    'nim'             => $record->nim,
                    'email'           => $record->email,
                    'no_hp'           => $record->no_hp,
                    'link_drive'      => $record->link_drive, 
                    'catatan_admin'   => $record->catatan_admin,   
                    'status_verifikasi' => $record->status_verifikasi,         
                    'tanggal_mulai'   => $record->tanggal_mulai,
                    'tanggal_selesai' => $record->tanggal_selesai,

                    'file_sertifikat' => $record->file_sertifikat ?? null,

                ]
            );
        }
    }
}
