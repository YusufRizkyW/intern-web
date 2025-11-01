<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranMagang extends Model
{
    protected $table = 'pendaftaran_magang';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'agency',
        'nim',
        'email',
        'no_hp',
        'status_verifikasi',
        'tipe_periode',
        'durasi_bulan',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function berkas()
    {
        return $this->hasMany(BerkasPendaftaran::class, 'pendaftaran_id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    use HasFactory;
}

