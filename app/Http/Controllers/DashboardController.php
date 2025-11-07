<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendaftaranMagang; // pastikan model ini sudah ada

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik umum
        $stats = [
            'total_pendaftar' => PendaftaranMagang::count(),
            'sedang_diproses' => PendaftaranMagang::whereIn('status', [
                'pending', 'revisi', 'diterima', 'aktif',
            ])->count(),
            'selesai' => PendaftaranMagang::where('status', 'selesai')->count(),
        ];

        // Data pendaftaran terbaru user (kalau sudah login)
        $pendaftaranTerbaru = null;

        if (auth()->check()) {
            $pendaftaranTerbaru = PendaftaranMagang::with('agency') // sesuaikan relasi di model
                ->where('user_id', auth()->id())
                ->latest()
                ->first();
        }

        // kirim ke view
        return view('dashboard', compact('stats', 'pendaftaranTerbaru'));
    }
}
