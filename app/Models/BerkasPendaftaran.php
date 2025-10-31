<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasPendaftaran extends Model
{
    protected $table = 'berkas_pendaftaran';

    protected $fillable = [
        'pendaftaran_id',
        'jenis_berkas',
        'path_file',
        'valid',
        'catatan_admin',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranMagang::class, 'pendaftaran_id');
    }

    use HasFactory;
}

