<?php

namespace App\Observers;

use App\Models\PendaftaranMagang;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;


class PendaftaranMagangObserver
{
    protected WhatsAppService $wa;

    public function __construct()
    {
        $this->wa = new WhatsAppService();
    }

    /**
     * Handle the PendaftaranMagang "updated" event.
     */
    public function updated(PendaftaranMagang $pendaftaran)
    {
        // cek jika status berubah
        if ($pendaftaran->wasChanged('status_verifikasi')) {
            $old = $pendaftaran->getOriginal('status_verifikasi');
            $new = $pendaftaran->status_verifikasi;

            // only send for specific statuses
            $allowed = ['revisi', 'diterima', 'ditolak', 'aktif', 'selesai', 'batal'];

            if (in_array($new, $allowed, true)) {
                // Format pesan sesuai status
                $message = $this->buildMessage($pendaftaran, $old, $new);

                // Ambil nomor telepon dari kolom no_hp
                $noHp = $pendaftaran->no_hp ?? $pendaftaran->user->phone ?? null;

                if ($noHp) {
                    $this->wa->send($noHp, $message);
                } else {
                    Log::warning('PendaftaranMagangObserver: no_hp not set; WA not sent', [
                        'pendaftaran_id' => $pendaftaran->id,
                        'status' => $new,
                    ]);
                }
            }
        }
    }

    protected function buildMessage(PendaftaranMagang $p, $oldStatus, $newStatus): string
    {
        $nama = $p->nama_lengkap ?? 'Peserta';
        $siteName = config('app.name');
        $base = "Halo {$nama},\nStatus pendaftaran magang Anda di {$siteName} telah berubah.";

        switch ($newStatus) {
            case 'revisi':
                return $base . "\nStatus: *Perlu Revisi*\nCatatan dari admin : " . ($p->catatan_admin ?? 'Tidak ada catatan.') . "\nSilakan unggah berkas/perbaiki data sesuai instruksi.";
            case 'diterima':
                return $base . "\nStatus: *Diterima*\nSelamat! Pendaftaran Anda telah diterima. Cek halaman *Status Pendaftaran* untuk detail selanjutnya.";
            case 'ditolak':
                return $base . "\nStatus: *Ditolak*\nMaaf, pendaftaran Anda tidak dapat kami terima. \nAlasan: " . ($p->catatan_admin ?? 'Tidak ada keterangan.');
            case 'aktif':
                return $base . "\nStatus: *Sedang Magang (Aktif)*\nInformasi lebih lanjut silahkan cek *Status Pendaftaran* atau WA.";
            case 'selesai':
                return $base . "\nStatus: *Selesai*\nTerima kasih sudah melaksanakan magang. Semoga pengalaman bermanfaat!";
            case 'batal':
                return $base . "\nStatus: *Dibatalkan*\nPendaftaran magang Anda telah dibatalkan. \nAlasan: " . ($p->catatan_admin ?? 'Tidak ada keterangan.');
            default:
                return $base . "\nStatus: {$newStatus}";
        }
    }
    
}