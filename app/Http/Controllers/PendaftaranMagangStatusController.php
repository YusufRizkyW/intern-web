<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PendaftaranMagang;
use App\Models\BerkasPendaftaran;

class PendaftaranMagangStatusController extends Controller
{
    // Halaman status pendaftaran
    public function show(Request $request)
    {
        $userId = $request->user()->id;

        // 1. cek dulu: sudah diarsipkan belum?
        // $riwayat = \App\Models\RiwayatMagang::where('user_id', $userId)->latest()->first();

        // if ($riwayat) {
        //     // kalau sudah punya riwayat → langsung ke halaman riwayat
        //     return redirect()->route('riwayat.user.index');
        // }

        $pendaftaran = PendaftaranMagang::where('user_id', $userId)
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return view('pendaftaran.status', [
                'pendaftaran' => null,
                'berkas' => [],
            ]);
        }

        $berkas = BerkasPendaftaran::where('pendaftaran_id', $pendaftaran->id)->get();

        return view('pendaftaran.status', [
            'pendaftaran' => $pendaftaran,
            'berkas' => $berkas,
        ]);
    }

    // Upload ulang satu dokumen yg invalid
    public function reuploadBerkas(Request $request, $id)
    {
        // 1. Ambil record berkas berdasarkan ID
        $berkas = BerkasPendaftaran::findOrFail($id);

        // 2. Pastikan berkas ini milik user yang lagi login
        $pendaftaran = PendaftaranMagang::where('id', $berkas->pendaftaran_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$pendaftaran) {
            // artinya user coba utak-atik ID berkas orang lain
            abort(403, 'Tidak boleh mengubah berkas ini.');
        }

        // 3. Validasi file baru (hanya PDF max 2MB)
        $validated = $request->validate([
            'file_baru' => 'required|file|mimes:pdf|max:2048',
        ]);

        // 4. Upload file baru ke storage (taro di folder sama: /berkas_pendaftaran/{pendaftaran_id}/{jenis_berkas})
        $newPath = $request->file('file_baru')->store(
            "berkas_pendaftaran/{$pendaftaran->id}/{$berkas->jenis_berkas}",
            'public'
        );

        // 5. Hapus file lama dari storage biar gak numpuk
        if ($berkas->path_file && Storage::disk('public')->exists($berkas->path_file)) {
            Storage::disk('public')->delete($berkas->path_file);
        }

        // 6. Update row berkas:
        //    - path_file diganti ke file baru
        //    - valid -> balik jadi 'pending' (nunggu admin review lagi)
        //    - catatan_admin direset
        $berkas->update([
            'path_file'     => $newPath,
            'valid'         => 'pending',
            'catatan_admin' => null,
        ]);

        // 7. Karena ada revisi baru masuk, status_verifikasi pendaftaran kita ubah jadi "pending" juga
        //    (opsional tapi ini bagus, supaya admin tau user sudah kirim revisi)
        $pendaftaran->update([
            'status_verifikasi' => 'pending',
        ]);

        // 8. Balikkan pesan sukses
        return redirect()
            ->route('pendaftaran.status')
            ->with('success', 'Dokumen berhasil diupload ulang. Menunggu verifikasi admin.');
    }
}
