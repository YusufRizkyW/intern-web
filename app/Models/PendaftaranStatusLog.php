<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendaftaranStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_magang_id',
        'admin_user_id',
        'status_lama',
        'status_baru',
        'catatan',
    ];

    public function pendaftaranMagang(): BelongsTo
    {
        return $this->belongsTo(PendaftaranMagang::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
