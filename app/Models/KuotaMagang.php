<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KuotaMagang extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan', 
        'kuota_maksimal',
        'kuota_terisi',
        'is_active',
        'catatan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'kuota_maksimal' => 'integer',
        'kuota_terisi' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Ambil kuota untuk periode tertentu
     */
    public static function getKuotaForPeriode($tahun, $bulan)
    {
        return self::where('tahun', $tahun)
                  ->where('bulan', $bulan)
                  ->where('is_active', true)
                  ->first();
    }

    /**
     * Cek apakah kuota masih tersedia
     */
    public function isKuotaAvailable($jumlahPeserta = 1): bool
    {
        return $this->is_active && 
               ($this->kuota_terisi + $jumlahPeserta) <= $this->kuota_maksimal;
    }

    /**
     * Tambah kuota terisi
     */
    public function addKuotaTerisi($jumlahPeserta): void
    {
        $this->increment('kuota_terisi', $jumlahPeserta);
    }

    /**
     * Kurangi kuota terisi
     */
    public function reduceKuotaTerisi($jumlahPeserta): void
    {
        $this->decrement('kuota_terisi', $jumlahPeserta);
    }

    /**
     * Persentase kuota terpakai
     */
    public function getPersentaseKuotaAttribute(): float
    {
        if ($this->kuota_maksimal <= 0) return 0;
        return ($this->kuota_terisi / $this->kuota_maksimal) * 100;
    }

    /**
     * Sisa kuota
     */
    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota_maksimal - $this->kuota_terisi);
    }
    /**
     * Nama bulan dalam bahasa Indonesia (static method)
     */
    public static function getNamaBulan($bulan): string
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $namaBulan[$bulan] ?? 'Unknown';
    }

    /**
     * Nama bulan dalam bahasa Indonesia (instance method)
     */
    public function getNamaBulanAttribute(): string
    {
        return self::getNamaBulan($this->bulan);
    }
    /**
     * Format periode (Januari 2024)
     */
    public function getPeriodeAttribute(): string
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    /**
     * Event listener untuk validasi duplikasi dan konsistensi data
     */
    protected static function booted(): void
    {
        static::saving(function ($model) {
            // Validasi duplicate periode
            $exists = self::where('tahun', $model->tahun)
                ->where('bulan', $model->bulan)
                ->where('id', '!=', $model->id ?? 0)
                ->exists();

            if ($exists) {
                $namaBulan = self::getNamaBulan($model->bulan);
                throw new \Exception("Kuota untuk periode {$namaBulan} {$model->tahun} sudah ada!");
            }

            // Validasi kuota_maksimal >= kuota_terisi
            if ($model->kuota_maksimal < $model->kuota_terisi) {
                throw new \Exception("Kuota maksimal ({$model->kuota_maksimal}) tidak boleh lebih kecil dari kuota terisi ({$model->kuota_terisi})");
            }

            // Pastikan kuota_terisi tidak negatif
            if ($model->kuota_terisi < 0) {
                $model->kuota_terisi = 0;
            }
        });
    }

    /**
     * Auto-generate kuota untuk beberapa bulan ke depan
     */
    public static function generateKuotaRange($startYear, $startMonth, $totalMonths = 12, $defaultKuota = 50)
    {
        $created = [];

        for ($i = 0; $i < $totalMonths; $i++) {
            $month = $startMonth + $i;
            $year = $startYear;

            // Handle year overflow
            while ($month > 12) {
                $month -= 12;
                $year++;
            }

            // Skip jika sudah ada
            if (self::where('tahun', $year)->where('bulan', $month)->exists()) {
                continue;
            }

            $kuota = self::create([
                'tahun' => $year,
                'bulan' => $month,
                'kuota_maksimal' => $defaultKuota,
                'kuota_terisi' => 0,
                'is_active' => true,
                'catatan' => 'Kuota otomatis untuk ' . self::getNamaBulan($month) . ' ' . $year,
            ]);

            $created[] = $kuota;
        }

        return $created;
    }

    /**
     * Recalculate kuota terisi berdasarkan data pendaftaran aktual
     */
    public function recalculateKuotaTerisi(): void
    {
        $totalPeserta = 0;

        // Ambil semua pendaftaran yang menggunakan kuota untuk periode ini
        $pendaftarans = \App\Models\PendaftaranMagang::whereIn('status_verifikasi', ['diterima', 'aktif', 'selesai'])
            ->get()
            ->filter(function ($pendaftaran) {
                $periodeMagang = $pendaftaran->periode_magang;
                foreach ($periodeMagang as $periode) {
                    if ($periode['tahun'] == $this->tahun && $periode['bulan'] == $this->bulan) {
                        return true;
                    }
                }
                return false;
            });

        foreach ($pendaftarans as $pendaftaran) {
            $totalPeserta += $pendaftaran->jumlah_peserta;
        }

        $this->update(['kuota_terisi' => $totalPeserta]);
    }
}