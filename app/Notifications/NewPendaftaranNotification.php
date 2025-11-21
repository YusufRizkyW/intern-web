<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PendaftaranMagang;
use Carbon\Carbon;

class NewPendaftaranNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected PendaftaranMagang $pendaftaran) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $p = $this->pendaftaran;

        // Format tanggal yang lebih user-friendly
        $tanggalMulai = $p->tanggal_mulai ? Carbon::parse($p->tanggal_mulai)->format('d M Y') : '-';
        $tanggalSelesai = $p->tanggal_selesai ? Carbon::parse($p->tanggal_selesai)->format('d M Y') : '-';
        
        // Nama user dengan fallback
        $namaUser = $p->user?->name ?? $p->nama_lengkap ?? 'User';
        
        // Agency/instansi dengan fallback
        $agency = $p->agency ?? 'Instansi tidak diketahui';

        return [
            'title' => 'Pendaftaran Magang Baru',
            'body'  => "{$namaUser} dari {$agency} mendaftar magang periode {$tanggalMulai} — {$tanggalSelesai}",
            'pendaftaran_id' => $p->id,
            'user_name' => $namaUser,
            'agency' => $agency,
            'periode' => "{$tanggalMulai} — {$tanggalSelesai}",
            'tipe_pendaftaran' => $p->tipe_pendaftaran ?? 'individu',
            'status' => $p->status_verifikasi ?? 'pending',
            'created_at' => $p->created_at?->toISOString(),
            'url' => $this->getNotificationUrl($p),
        ];
    }

    /**
     * Get the mail representation of the notification (opsional untuk nanti).
     */
    public function toMail($notifiable): MailMessage
    {
        $p = $this->pendaftaran;
        $namaUser = $p->user?->name ?? $p->nama_lengkap ?? 'User';
        
        return (new MailMessage)
            ->subject('Pendaftaran Magang Baru')
            ->greeting('Halo Admin!')
            ->line("Ada pendaftaran magang baru dari {$namaUser}.")
            ->line("Periode: {$p->tanggal_mulai} — {$p->tanggal_selesai}")
            ->line("Instansi: {$p->agency}")
            ->action('Lihat Detail', $this->getNotificationUrl($p))
            ->line('Silakan segera review pendaftaran ini.');
    }

    /**
     * Get the appropriate URL for the notification
     */
    private function getNotificationUrl(PendaftaranMagang $pendaftaran): string
    {
        // Coba route yang mungkin ada
        $possibleRoutes = [
            'filament.admin.resources.pendaftaran-magangs.view',
            'filament.admin.resources.pendaftaran-magangs.edit',
            'admin.pendaftaran.show',
            'pendaftaran.show',
        ];

        foreach ($possibleRoutes as $routeName) {
            if (\Route::has($routeName)) {
                try {
                    return route($routeName, $pendaftaran->id);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        // Fallback ke admin dashboard jika tidak ada route yang cocok
        if (\Route::has('filament.admin.pages.dashboard')) {
            return route('filament.admin.pages.dashboard');
        }

        // Ultimate fallback
        return url('/admin');
    }

    /**
     * Get the notification's database type (opsional).
     */
    public function databaseType(object $notifiable): string
    {
        return 'new-pendaftaran';
    }
}
