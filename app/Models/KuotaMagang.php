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
}