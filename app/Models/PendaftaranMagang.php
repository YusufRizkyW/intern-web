<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PendaftaranMagang extends Model
{
    protected $table = 'pendaftaran_magangs';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'agency',
        'nim',
        'email',
        'no_hp',
        'tipe_pendaftaran',
        'jumlah_anggota',   
        'link_drive',
        'catatan_admin',
        'status_verifikasi',
        'tipe_periode',
        'durasi_bulan',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->hasMany(PendaftaranMagangMember::class, 'pendaftaran_magang_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(PendaftaranStatusLog::class);
    }

    /**
     * Hitung jumlah peserta dalam pendaftaran ini
     */
    public function getJumlahPesertaAttribute(): int
    {
        if ($this->tipe_pendaftaran === 'individu') {
            return 1;
        }

        // Untuk tim: hitung ketua + anggota
        return $this->members()->count();
    }

    /**
     * Dapatkan semua periode (bulan) yang tercover oleh magang ini
     */
    public function getPeriodeMagangAttribute(): array
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) {
            // Jika tidak ada tanggal spesifik, gunakan created_at sebagai referensi
            $tanggal = $this->created_at;
            return [
                [
                    'tahun' => $tanggal->year,
                    'bulan' => $tanggal->month
                ]
            ];
        }

        $mulai = \Carbon\Carbon::parse($this->tanggal_mulai);
        $selesai = \Carbon\Carbon::parse($this->tanggal_selesai);
        $periode = [];

        $current = $mulai->copy()->startOfMonth();
        $end = $selesai->copy()->startOfMonth();

        while ($current <= $end) {
            $periode[] = [
                'tahun' => $current->year,
                'bulan' => $current->month
            ];
            $current->addMonth();
        }

        return $periode;
    }

    // Event listener untuk update kuota otomatis
    protected static function booted(): void
    {
        // Event listener untuk update kuota otomatis
        static::updating(function (PendaftaranMagang $pendaftaran) {
            $statusLama = $pendaftaran->getOriginal('status_verifikasi');
            $statusBaru = $pendaftaran->status_verifikasi;

            // ✅ PERBAIKAN: Status yang masih AKTIF menggunakan kuota
            $statusTerpakai = ['diterima', 'aktif']; // Hapus 'selesai'
            
            // Status yang sudah FINAL tidak menggunakan kuota lagi
            $statusFinal = ['selesai', 'batal', 'arsip'];
            
            $wasUsingQuota = in_array($statusLama, $statusTerpakai);
            $willUseQuota = in_array($statusBaru, $statusTerpakai);

            // Jika dari tidak-terpakai ke terpakai
            if (!$wasUsingQuota && $willUseQuota) {
                $pendaftaran->updateKuotaMultiPeriode('add');
            }

            // ✅ PERBAIKAN: Kembalikan kuota jika berubah ke status final
            if ($wasUsingQuota && !$willUseQuota) {
                $pendaftaran->updateKuotaMultiPeriode('reduce');
            }
            

            // Log perubahan status (tetap sama)
            if ($pendaftaran->isDirty('status_verifikasi')) {
                \App\Models\PendaftaranStatusLog::create([
                    'pendaftaran_magang_id' => $pendaftaran->id,
                    'admin_user_id' => auth()->id(),
                    'status_lama' => $statusLama,
                    'status_baru' => $statusBaru,
                    'catatan' => $pendaftaran->catatan_admin,
                ]);
            }

            // Pindahkan ke riwayat (tetap sama)
            $statusRiwayat = ['selesai', 'batal', 'arsip'];
            if (in_array($statusBaru, $statusRiwayat) && !in_array($statusLama, $statusRiwayat)) {
                \App\Models\RiwayatMagang::create([
                    'pendaftaran_magang_id' => $pendaftaran->id,
                    'user_id' => $pendaftaran->user_id,
                    'nama_lengkap' => $pendaftaran->nama_lengkap,
                    'agency' => $pendaftaran->agency,
                    'nim' => $pendaftaran->nim,
                    'email' => $pendaftaran->email,
                    'no_hp' => $pendaftaran->no_hp,
                    'link_drive' => $pendaftaran->link_drive,
                    'catatan_admin' => $pendaftaran->catatan_admin,
                    'status_verifikasi' => $statusBaru,
                    'tanggal_mulai' => $pendaftaran->tanggal_mulai,
                    'tanggal_selesai' => $pendaftaran->tanggal_selesai,
                ]);
            }
        });
    }

    /**
     * Update kuota untuk semua periode yang tercover
     */
    private function updateKuotaMultiPeriode(string $action): void
    {
        $jumlahPeserta = $this->jumlah_peserta;
        $periodeMagang = $this->periode_magang;

        foreach ($periodeMagang as $periode) {
            $kuota = KuotaMagang::getKuotaForPeriode(
                $periode['tahun'],
                $periode['bulan']
            );

            if ($kuota) {
                if ($action === 'add') {
                    $kuota->addKuotaTerisi($jumlahPeserta);
                } elseif ($action === 'reduce') {
                    $kuota->reduceKuotaTerisi($jumlahPeserta);
                }
            }
        }
    }

    /**
     * Validasi kuota sebelum approve - cek semua periode
     */
    public function canBeApproved(): bool
    {
        // Hanya cek kuota jika akan mengubah dari tidak-terpakai ke terpakai
        $statusTerpakai = ['diterima', 'aktif', 'selesai'];
        
        if (in_array($this->status_verifikasi, $statusTerpakai)) {
            return true; // Status sudah terpakai kuota, tidak perlu cek lagi
        }

        $jumlahPeserta = $this->jumlah_peserta;
        $periodeMagang = $this->periode_magang;

        // Cek setiap periode, semua harus tersedia
        foreach ($periodeMagang as $periode) {
            $kuota = KuotaMagang::getKuotaForPeriode(
                $periode['tahun'],
                $periode['bulan']
            );

            if (!$kuota) {
                return false; // Tidak ada kuota untuk periode ini
            }

            if (!$kuota->isKuotaAvailable($jumlahPeserta)) {
                return false; // Kuota tidak cukup untuk periode ini
            }
        }

        return true; // Semua periode OK
    }

    /**
     * Get info periode yang bermasalah (untuk error message)
     */
    public function getPeriodeTidakTersediaAttribute(): ?array
    {
        $jumlahPeserta = $this->jumlah_peserta;
        $periodeMagang = $this->periode_magang;

        foreach ($periodeMagang as $periode) {
            $kuota = KuotaMagang::getKuotaForPeriode(
                $periode['tahun'],
                $periode['bulan']
            );

            if (!$kuota || !$kuota->isKuotaAvailable($jumlahPeserta)) {
                return [
                    'tahun' => $periode['tahun'],
                    'bulan' => $periode['bulan'],
                    'nama_bulan' => KuotaMagang::getNamaBulan($periode['bulan']),
                    'sisa_kuota' => $kuota ? $kuota->sisa_kuota : 0,
                    'dibutuhkan' => $jumlahPeserta
                ];
            }
        }

        return null;
    }

    // Method lama untuk backward compatibility
    private function updateKuota(string $action): void
    {
        $this->updateKuotaMultiPeriode($action);
    }

    

    
}

