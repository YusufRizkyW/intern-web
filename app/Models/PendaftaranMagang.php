<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    
    // public function members()
    // {
    //     return $this->hasMany(\App\Models\PendaftaranMagangMember::class);
    // }

    public function members()
    {
        return $this->hasMany(PendaftaranMagangMember::class, 'pendaftaran_magang_id');
    }
    


    use HasFactory;
}

