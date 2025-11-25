<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'link_drive'       => ['required','url','regex:/^https:\/\/(drive|docs)\.google\.com/'],
            'agency'           => 'required|string|max:255',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        // 2. VALIDASI PER TIPE PENDAFTARAN
        if ($request->tipe_pendaftaran === 'individu') {
            $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'agency'       => 'required|string|max:255',
                'nim'          => 'required|string|max:50',
                'email'        => 'required|email|max:255',
                'no_hp'        => 'required|string|max:30',
            ]);
        } else {
            // tim / rombongan
            $request->validate([
                'anggota'        => 'required|array|min:1',
                'anggota.0.nama' => 'required|string|max:255', // ketua
                'anggota.*.nim'  => 'required|string|max:50',
                'anggota.*.email'=> 'required|email|max:255',
                'anggota.*.no_hp'=> 'required|string|max:30',
            ]);
        }

        // 3. PERIODE - selalu gunakan tanggal
        $tanggalMulai   = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

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

    public function edit(PendaftaranMagang $pendaftaran)
    {
        // Cek apakah pendaftaran milik user yang login
        if ($pendaftaran->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah status masih pending
        if (!in_array($pendaftaran->status_verifikasi, ['pending', 'revisi'], true)) {
            return redirect()
                ->route('pendaftaran.status')
                ->with('error', 'Pendaftaran tidak dapat diedit. Status saat ini: ' . $pendaftaran->status_verifikasi);
        }

        return view('pendaftaran.edit', compact('pendaftaran'));
    }

    public function update(Request $request, PendaftaranMagang $pendaftaran)
    {
        // Cek apakah pendaftaran milik user yang login
        if ($pendaftaran->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah status masih pending
        if (!in_array($pendaftaran->status_verifikasi, ['pending', 'revisi'], true)) {
            return redirect()
                ->route('pendaftaran.status')
                ->with('error', 'Pendaftaran tidak dapat diedit. Status saat ini: ' . $pendaftaran->status_verifikasi);
        }

        // 1. VALIDASI UMUM (selalu wajib)
        $request->validate([
            'tipe_pendaftaran' => 'required|in:individu,tim',
            'link_drive'       => ['required','url','regex:/^https:\/\/(drive|docs)\.google\.com/'],
            'agency'           => 'required|string|max:255',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        // 2. VALIDASI PER TIPE PENDAFTARAN
        if ($request->tipe_pendaftaran === 'individu') {
            $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'nim'          => 'required|string|max:50',
                'email'        => 'required|email|max:255',
                'no_hp'        => 'required|string|max:20',
            ]);
        } else {
            $request->validate([
                'anggota'                    => 'required|array|min:1',
                'anggota.*.nama'             => 'required|string|max:255',
                'anggota.*.nim'              => 'required|string|max:50',
                'anggota.*.email'            => 'required|email|max:255',
                'anggota.*.no_hp'            => 'required|string|max:20',
            ]);
        }

        // 3. PERIODE - selalu gunakan tanggal
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        DB::beginTransaction();
        try {
            // Update data pendaftaran utama
            $pendaftaran->update([
                'tipe_pendaftaran' => $request->tipe_pendaftaran,
                'nama_lengkap'     => $request->nama_lengkap ?? $request->anggota[0]['nama'] ?? '',
                'nim'              => $request->nim ?? $request->anggota[0]['nim'] ?? null,
                'email'            => $request->email ?? $request->anggota[0]['email'] ?? '',
                'no_hp'            => $request->no_hp ?? $request->anggota[0]['no_hp'] ?? '',
                'agency'           => $request->agency,
                'tanggal_mulai'    => $tanggalMulai,
                'tanggal_selesai'  => $tanggalSelesai,
                'link_drive'       => $request->link_drive,

                // Reset status verifikasi ke pending setelah update
                'status_verifikasi' => 'pending',
            ]);

            // Update anggota tim (hapus yang lama, buat yang baru)
            if ($request->tipe_pendaftaran === 'tim' && $request->has('anggota')) {
                // Hapus anggota lama
                $pendaftaran->members()->delete();
                
                // Buat anggota baru
                foreach ($request->anggota as $anggota) {
                    $pendaftaran->members()->create([
                        'nama_anggota'  => $anggota['nama'],
                        'nim_anggota'   => $anggota['nim'],
                        'email_anggota' => $anggota['email'],
                        'no_hp_anggota' => $anggota['no_hp'],
                    ]);
                }
            }

            DB::commit();
            
            return redirect()
                ->route('pendaftaran.status')
                ->with('success', 'Pendaftaran berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan pendaftaran (hanya untuk status pending)
     */
    public function destroy(PendaftaranMagang $pendaftaran)
    {
        // Pastikan hanya user yang memiliki pendaftaran ini yang bisa membatalkan
        if ($pendaftaran->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hanya bisa dibatalkan jika status masih pending
        if ($pendaftaran->status_verifikasi !== 'pending') {
            return redirect()->route('pendaftaran.status')
                ->with('error', 'Pendaftaran tidak dapat dibatalkan karena sudah diproses admin.');
        }

        try {
            DB::beginTransaction();

            // Hapus data members jika ada (untuk tipe tim)
            if ($pendaftaran->tipe_pendaftaran === 'tim') {
                $pendaftaran->members()->delete();
            }

            // Simpan info untuk flash message
            $nama = $pendaftaran->nama_lengkap;
            $agency = $pendaftaran->agency;

            // Hapus pendaftaran utama
            $pendaftaran->delete();

            DB::commit();

            return redirect()->route('pendaftaran.status')
                ->with('success', "Pendaftaran magang atas nama {$nama} dari {$agency} berhasil dibatalkan dan dihapus dari sistem.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error membatalkan pendaftaran: ' . $e->getMessage());
            
            return redirect()->route('pendaftaran.status')
                ->with('error', 'Terjadi kesalahan saat membatalkan pendaftaran. Silakan coba lagi atau hubungi admin.');
        }
    }
}
