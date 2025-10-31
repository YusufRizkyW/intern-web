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
            'nim'             => 'nullable|string|max:50',
            'email'           => 'required|email|max:255',
            'no_hp'           => 'nullable|string|max:30',

            // file wajib pdf max 2MB
            'cv'              => 'required|file|mimes:pdf|max:2048',
            'surat_pengantar' => 'required|file|mimes:pdf|max:2048',
            'ktm'             => 'required|file|mimes:pdf|max:2048',

            // periode magang
            'tipe_periode'    => 'required|in:durasi,tanggal',

            'durasi_bulan'    => 'nullable|integer|min:1|max:12',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
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

        // Inisialisasi path file untuk jaga-jaga kalau gagal di tengah
        $cvPath = null;
        $spPath = null;
        $ktmPath = null;

        DB::beginTransaction();

        try {
            // 3. Simpan data pendaftaran utama
            $pendaftaran = PendaftaranMagang::create([
                'user_id'           => auth()->id(),
                'nama_lengkap'      => $validated['nama_lengkap'],
                'nim'               => $validated['nim'] ?? null,
                'email'             => $validated['email'],
                'no_hp'             => $validated['no_hp'] ?? null,
                'status_verifikasi' => 'pending',

                'tipe_periode'      => $validated['tipe_periode'],
                'durasi_bulan'      => $validated['durasi_bulan'] ?? null,
                'tanggal_mulai'     => $validated['tanggal_mulai'] ?? null,
                'tanggal_selesai'   => $validated['tanggal_selesai'] ?? null,
            ]);

            // 4. Simpan file secara fisik ke storage/app/public/...
            //    Folder dibedakan per pendaftar biar rapi
            $cvPath = $request->file('cv')
                ->store("berkas_pendaftaran/{$pendaftaran->id}/cv", 'public');

            $spPath = $request->file('surat_pengantar')
                ->store("berkas_pendaftaran/{$pendaftaran->id}/surat_pengantar", 'public');

            $ktmPath = $request->file('ktm')
                ->store("berkas_pendaftaran/{$pendaftaran->id}/ktm", 'public');

            // 5. Catat metadata file di tabel berkas_pendaftaran
            BerkasPendaftaran::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_berkas'   => 'cv',
                'path_file'      => $cvPath,
                'valid'          => 'pending',
            ]);

            BerkasPendaftaran::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_berkas'   => 'surat_pengantar',
                'path_file'      => $spPath,
                'valid'          => 'pending',
            ]);

            BerkasPendaftaran::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_berkas'   => 'ktm',
                'path_file'      => $ktmPath,
                'valid'          => 'pending',
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            // 6. Kalau gagal di tengah, hapus file yang sudah terupload
            if ($cvPath) Storage::disk('public')->delete($cvPath);
            if ($spPath) Storage::disk('public')->delete($spPath);
            if ($ktmPath) Storage::disk('public')->delete($ktmPath);

            throw $e;
        }

        // 7. Redirect balik dengan flash message sukses
        // return redirect()
        //     ->route('pendaftaran.create')
        //     ->with('success', 'Pendaftaran berhasil dikirim. Silakan tunggu verifikasi admin.');

        return back()->with('success', 'Pendaftaran berhasil dikirim. Silakan tunggu verifikasi admin.');
    }
}
