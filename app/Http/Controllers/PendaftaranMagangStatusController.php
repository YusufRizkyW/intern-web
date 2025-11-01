<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendaftaranMagang;
use App\Models\RiwayatMagang;

class PendaftaranMagangStatusController extends Controller
{
    public function show(Request $request)
    {
        $userId = $request->user()->id;

        // pendaftaran terakhir user
        $pendaftaran = PendaftaranMagang::where('user_id', $userId)
            ->latest()
            ->first();

        // riwayat terakhir (kalau mau ditampilin di mode kosong)
        $riwayatTerbaru = RiwayatMagang::where('user_id', $userId)
            ->orderByDesc('tanggal_selesai')
            ->first();

        // status yang masih dianggap "aktif"
        $statusAktif = ['pending', 'revisi', 'diterima', 'aktif'];

        // status yang sudah selesai / tidak ditampilkan lagi
        $statusFinal = ['selesai', 'batal', 'arsip'];

        // 1. belum pernah daftar
        if (! $pendaftaran) {
            return view('pendaftaran.status', [
                'pendaftaran'     => null,
                'riwayat_terbaru' => $riwayatTerbaru,
            ]);
        }

        // 2. kalau sudah status final → reset tampilan
        if (in_array($pendaftaran->status_verifikasi, $statusFinal, true)) {
            return view('pendaftaran.status', [
                'pendaftaran'     => null,
                'riwayat_terbaru' => $riwayatTerbaru,
            ]);
        }

        // 3. kalau status masih aktif → kirim datanya ke view
        if (in_array($pendaftaran->status_verifikasi, $statusAktif, true)) {
            return view('pendaftaran.status', [
                'pendaftaran'     => $pendaftaran,
                'riwayat_terbaru' => $riwayatTerbaru,
            ]);
        }

        // 4. fallback (kalau ada status aneh)
        return view('pendaftaran.status', [
            'pendaftaran'     => null,
            'riwayat_terbaru' => $riwayatTerbaru,
        ]);
    }
}
