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
        'file_sertifikat',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranMagang::class, 'pendaftaran_magang_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    use HasFactory;
}
