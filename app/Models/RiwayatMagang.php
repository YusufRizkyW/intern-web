<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatMagang extends Model
{
    protected $table = 'riwayat_magang';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'agency',
        'nim',
        'email',
        'no_hp',
        'tanggal_mulai',
        'tanggal_selesai',
        'catatan_admin',
        'file_sertifikat',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
