<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranMagangMember extends Model
{
    protected $table = 'pendaftaran_magang_members';

    protected $fillable = [
        'pendaftaran_magang_id',
        'nama_anggota',
        'agency_anggota',
        'nim_anggota',
        'email_anggota',
        'no_hp_anggota',
        'is_ketua',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranMagang::class, 'pendaftaran_magang_id');
    }
}
