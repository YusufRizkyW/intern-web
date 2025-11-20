<?php

namespace App\Observers;

use App\Models\PendaftaranMagang;
use App\Models\User;
use App\Notifications\NewPendaftaranNotification;
use Illuminate\Support\Facades\Log;

class PendaftaranMagangObserver
{
    public function created(PendaftaranMagang $pendaftaran): void
    {
        try {
            // Ambil semua admin (sesuaikan dengan role system Anda)
            $admins = User::where('email', 'like', '%admin%')
                         ->orWhere('role', 'admin')
                         ->get();

            Log::info('PendaftaranMagangObserver: created', [
                'pendaftaran_id' => $pendaftaran->id,
                'admins_count' => $admins->count()
            ]);

            foreach ($admins as $admin) {
                $admin->notify(new NewPendaftaranNotification($pendaftaran));
                Log::info('Notification sent', [
                    'admin_id' => $admin->id,
                    'pendaftaran_id' => $pendaftaran->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('PendaftaranMagangObserver error: ' . $e->getMessage());
        }
    }
}