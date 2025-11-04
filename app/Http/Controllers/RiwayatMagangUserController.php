<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatMagang;

class RiwayatMagangUserController extends Controller
{
    public function index(Request $request)
    {
        // ambil semua riwayat milik user yang login
        $riwayat = RiwayatMagang::where('user_id', $request->user()->id)
            ->orderByDesc('tanggal_selesai')
            ->orderByDesc('created_at')
            ->get();

        $riwayat = RiwayatMagang::with(['pendaftaranMagang.members'])
            ->where('user_id', auth()->id())
            ->orderByDesc('tanggal_selesai')
            ->get();


        return view('riwayat.user-index', [
            'riwayat' => $riwayat,
        ]);
    }
}
