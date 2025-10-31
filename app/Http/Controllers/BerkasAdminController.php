<?php

namespace App\Http\Controllers;

use App\Models\BerkasPendaftaran;
use Illuminate\Support\Facades\Storage;

class BerkasAdminController extends Controller
{
    public function view($id)
    {
        $berkas = BerkasPendaftaran::findOrFail($id);

        // Ambil file dari disk 'public'
        $fullPath = Storage::disk('public')->path($berkas->path_file);

        // Tampilkan inline sebagai PDF
        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
