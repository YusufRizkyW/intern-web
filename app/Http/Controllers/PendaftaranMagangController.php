<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PendaftaranMagang;

class PendaftaranMagangController extends Controller
{
    public function create()
    {
        return view('pendaftaran.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // 1. VALIDASI UMUM (selalu wajib)
        $request->validate([
            'tipe_pendaftaran' => 'required|in:individu,tim',
            'tipe_periode'     => 'required|in:durasi,tanggal',
            'link_drive'       => ['required','url','regex:/^https:\/\/(drive|docs)\.google\.com/'],
            'agency'           => 'required|string|max:255',
        ]);

        // 2. VALIDASI PER TIPE PENDAFTARAN
        if ($request->tipe_pendaftaran === 'individu') {
            $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'agency'       => 'required|string|max:255',
                'nim'          => 'nullable|string|max:50',
                'email'        => 'nullable|email|max:255',
                'no_hp'        => 'nullable|string|max:30',
            ]);
        } else {
            // tim / rombongan
            $request->validate([
                'anggota'        => 'required|array|min:1',
                'anggota.0.nama' => 'required|string|max:255', // ketua
                'anggota.*.nim'  => 'nullable|string|max:50',
                'anggota.*.email'=> 'nullable|email|max:255',
                'anggota.*.no_hp'=> 'nullable|string|max:30',
            ]);
        }

        // 3. VALIDASI PERIODE
        if ($request->tipe_periode === 'durasi') {
            $request->validate([
                'durasi_bulan' => 'required|integer|min:1|max:12',
            ]);
            $tanggalMulai   = null;
            $tanggalSelesai = null;
        } else {
            $request->validate([
                'tanggal_mulai'   => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            ]);
            $tanggalMulai   = $request->tanggal_mulai;
            $tanggalSelesai = $request->tanggal_selesai;
        }

        DB::beginTransaction();
        try {

            // 4. SIMPAN DATA
            if ($request->tipe_pendaftaran === 'individu') {

                $pendaftaran = PendaftaranMagang::create([
                    'user_id'           => auth()->id(),
                    'tipe_pendaftaran'  => 'individu',

                    // data peserta
                    'nama_lengkap'      => $request->nama_lengkap,
                    'agency'            => $request->agency,
                    'nim'               => $request->nim,
                    'email'             => $request->email,
                    'no_hp'             => $request->no_hp,

                    // berkas
                    'link_drive'        => $request->link_drive,

                    // periode
                    'tipe_periode'      => $request->tipe_periode,
                    'durasi_bulan'      => $request->tipe_periode === 'durasi' ? $request->durasi_bulan : null,
                    'tanggal_mulai'     => $tanggalMulai,
                    'tanggal_selesai'   => $tanggalSelesai,

                    // status awal
                    'status_verifikasi' => 'pending',
                ]);

            } else {
                // TIPE TIM
                // bersihkan anggota kosong
                $anggota = collect($request->anggota)
                    ->filter(fn($row) => !empty($row['nama']))
                    ->values();

                // pastikan masih ada minimal 1
                if ($anggota->isEmpty()) {
                    return back()
                        ->withErrors(['anggota.0.nama' => 'Minimal 1 anggota harus diisi.'])
                        ->withInput();
                }

                // simpan pendaftaran utama pakai anggota pertama sebagai ketua
                $pendaftaran = PendaftaranMagang::create([
                    'user_id'           => auth()->id(),
                    'tipe_pendaftaran'  => 'tim',

                    // pakai ketua
                    'nama_lengkap'      => $anggota[0]['nama'],
                    'agency'            => $request->agency,
                    'nim'               => $anggota[0]['nim'] ?? null,
                    'email'             => $anggota[0]['email'] ?? null,
                    'no_hp'             => $anggota[0]['no_hp'] ?? null,

                    // berkas
                    'link_drive'        => $request->link_drive,

                    // periode
                    'tipe_periode'      => $request->tipe_periode,
                    'durasi_bulan'      => $request->tipe_periode === 'durasi' ? $request->durasi_bulan : null,
                    'tanggal_mulai'     => $tanggalMulai,
                    'tanggal_selesai'   => $tanggalSelesai,

                    // status awal
                    'status_verifikasi' => 'pending',
                ]);

                // simpan anggota lain ke tabel relasi
                foreach ($anggota as $i => $member) {
                    $pendaftaran->members()->create([
                        'nama_anggota'    => $member['nama'] ?? null,
                        'agency_anggota'  => $request->agency,           // satu asal untuk tim
                        'nim_anggota'     => $member['nim'] ?? null,
                        'email_anggota'   => $member['email'] ?? null,
                        'no_hp_anggota'   => $member['no_hp'] ?? null,
                        'is_ketua'        => $i === 0,
                    ]);
                }


                // kalau kamu mau simpan jumlah_anggota di pendaftarannya:
                $pendaftaran->update([
                    'jumlah_anggota' => $anggota->count(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'Pendaftaran berhasil dikirim. Silakan tunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors('Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }
}
