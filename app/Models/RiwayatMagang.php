<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatMagang extends Model
{
    protected $table = 'riwayat_magangs';

    protected $fillable = [
        'pendaftaran_magang_id',
        'user_id',
        'nama_lengkap',
        'agency',
        'nim',
        'email',
        'no_hp',
        'link_drive',
        'catatan_admin',
        'status_verifikasi',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function pendaftaranMagang()
    {
        return $this->belongsTo(PendaftaranMagang::class, 'pendaftaran_magang_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function members()
    {
        return $this->hasMany(\App\Models\PendaftaranMagangMember::class);
    }


    protected static function booted(): void
    {
        static::saving(function (RiwayatMagang $riwayat) {
            $validStatuses = ['selesai', 'batal', 'arsip', 'ditolak'];
            if (!in_array($riwayat->status_verifikasi, $validStatuses)) {
                throw new \Exception("Status {$riwayat->status_verifikasi} tidak valid untuk riwayat magang.");
            }
        });
    }

    public function scopeSelesai($query)
    {
        return $query->where('status_verifikasi', 'selesai');
    }

    public function scopeBatal($query)
    {
        return $query->where('status_verifikasi', 'batal');
    }

    public function scopeArsip($query)
    {
        return $query->where('status_verifikasi', 'arsip');
    }
    
    public function scopeDitolak($query)
    {
        return $query->where('status_verifikasi', 'ditolak');
    }

    use HasFactory;
}
