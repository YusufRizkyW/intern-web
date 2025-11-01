<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\PendaftaranMagang;
use App\Models\BerkasPendaftaran;

class PendaftaranMagangController extends Controller
{
    public function create()
    {
        return view('pendaftaran.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi input dasar
        $validated = $request->validate([
            'nama_lengkap'    => 'required|string|max:255',
            'agency'          => 'required|string|max:255',
            'nim'             => 'required|string|max:50',
            'email'           => 'required|email|max:255',
            'no_hp'           => 'required|string|max:30',
            'link_drive'      => 'required|url|max:500',
            'catatan_admin'   => 'nullable|string|max:1000',
            'tipe_periode'    => 'required|in:durasi,tanggal',
            'durasi_bulan'    => 'nullable|integer|min:1|max:12|required_if:tipe_periode,durasi',
            'tanggal_mulai'   => 'nullable|date|required_if:tipe_periode,tanggal',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai|required_if:tipe_periode,tanggal',

            'link_drive' => [
                'required',
                'url',
                'regex:/^https:\/\/(drive|docs)\.google\.com/',
            ],

        ]);

        // 2. Validasi kondisional sesuai pilihan user
        if ($validated['tipe_periode'] === 'durasi') {
            if (!$request->filled('durasi_bulan')) {
                return back()
                    ->withErrors(['durasi_bulan' => 'Durasi magang wajib diisi.'])
                    ->withInput();
            }

            // kosongkan tanggal supaya konsisten di DB
            $validated['tanggal_mulai'] = null;
            $validated['tanggal_selesai'] = null;
        }

        if ($validated['tipe_periode'] === 'tanggal') {
            if (!$request->filled('tanggal_mulai') || !$request->filled('tanggal_selesai')) {
                return back()
                    ->withErrors(['tanggal_mulai' => 'Tanggal mulai & selesai wajib diisi.'])
                    ->withInput();
            }

            // kosongkan durasi supaya konsisten di DB
            $validated['durasi_bulan'] = null;
        }

        DB::beginTransaction();

        try {
            // 3. Simpan data pendaftaran utama
            $pendaftaran = PendaftaranMagang::create([
                'user_id'           => auth()->id(),
                'nama_lengkap'      => $validated['nama_lengkap'],
                'agency'            => $validated['agency'] ?? null,
                'nim'               => $validated['nim'] ?? null,
                'email'             => $validated['email'],
                'no_hp'             => $validated['no_hp'] ?? null,
                'link_drive'        => $validated['link_drive'],
                'status_verifikasi' => 'pending',
                'tipe_periode'      => $validated['tipe_periode'],
                'durasi_bulan'      => $validated['durasi_bulan'] ?? null,
                'tanggal_mulai'     => $validated['tanggal_mulai'] ?? null,
                'tanggal_selesai'   => $validated['tanggal_selesai'] ?? null,
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return back()->with('success', 'Pendaftaran berhasil dikirim. Silakan tunggu verifikasi admin.');
    }
}
