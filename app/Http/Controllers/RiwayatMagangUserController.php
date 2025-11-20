<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatMagang;

class RiwayatMagangUserController extends Controller
{
    public function index()
    {
        // ✅ TAMBAHKAN ORDERING DESCENDING BERDASARKAN TANGGAL TERBARU
        $riwayat = RiwayatMagang::where('user_id', auth()->id())
            ->with([
                'pendaftaranMagang:id,user_id,nama_lengkap,nim,agency,tipe_pendaftaran',
                'pendaftaranMagang.members:id,pendaftaran_magang_id,nama_anggota,nim_anggota,is_ketua'
            ])
            // Urutan berdasarkan yang terbaru dulu
            ->orderBy('created_at', 'desc')
            // Jika ada tanggal_selesai, gunakan itu sebagai secondary sort
            ->orderBy('tanggal_selesai', 'desc')
            // Jika ada tanggal_mulai, gunakan itu sebagai tertiary sort  
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('riwayat.user-index', compact('riwayat'));
    }
}
