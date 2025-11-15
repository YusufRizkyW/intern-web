<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Event untuk mencatat perubahan status
    protected static function booted(): void
    {
        static::updating(function (PendaftaranMagang $pendaftaran) {
            // Cek apakah status_verifikasi berubah
            if ($pendaftaran->isDirty('status_verifikasi')) {
                $statusLama = $pendaftaran->getOriginal('status_verifikasi');
                $statusBaru = $pendaftaran->status_verifikasi;
                
                // Simpan log perubahan status
                PendaftaranStatusLog::create([
                    'pendaftaran_magang_id' => $pendaftaran->id,
                    'admin_user_id' => auth()->id(), // ID admin yang sedang login
                    'status_lama' => $statusLama,
                    'status_baru' => $statusBaru,
                    'catatan' => $pendaftaran->catatan_admin,
                ]);
            }
        });
    }
}

