<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendaftaranMagang;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ---------- Konfigurasi status yang dianggap "aktif" ----------
        // Sesuaikan value dengan yang tersimpan di DB-mu
        $validStatuses = ['aktif']; // tambahkan 'disetujui','approved' jika perlu

        // ---------- STATISTIK umum ----------
        $stats = [
            'total_pendaftar' => PendaftaranMagang::count(),
            'sedang_diproses' => PendaftaranMagang::whereIn('status_verifikasi', [
                'pending', 'revisi', 'diterima', 'aktif',
            ])->count(),
            'selesai' => PendaftaranMagang::where('status_verifikasi', 'selesai')->count(),
        ];

        // ---------- Pendaftaran terbaru user (kalau login) ----------
        $pendaftaranTerbaru = null;
        if (auth()->check()) {
            $pendaftaranTerbaru = PendaftaranMagang::with('members')
                ->where('user_id', auth()->id())
                ->latest()
                ->first();
        }

        // ---------- KALENDER: bulan target ----------
        $monthParam = $request->query('month'); // "YYYY-MM"
        try {
            $currentMonth = $monthParam
                ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth()
                : Carbon::now()->startOfMonth();
        } catch (\Exception $e) {
            $currentMonth = Carbon::now()->startOfMonth();
        }

        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth   = $currentMonth->copy()->endOfMonth();

        // rentang tampilan kalender (full minggu)
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar   = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        // ---------- AMBIL PENDAFTARAN YANG MENYENTUH BULAN INI ----------
        // eager load members dan hitung members_count
        $pendaftaranGlobal = PendaftaranMagang::withCount('members')
            ->whereIn('status_verifikasi', $validStatuses)
            ->whereDate('tanggal_mulai', '<=', $endOfMonth->toDateString())
            ->whereDate('tanggal_selesai', '>=', $startOfMonth->toDateString())
            ->get();

        // bangun array: 'YYYY-MM-DD' => jumlah peserta (orang)
        $calendarGlobal = [];

        foreach ($pendaftaranGlobal as $p) {
            $pStart = Carbon::parse($p->tanggal_mulai);
            $pEnd   = Carbon::parse($p->tanggal_selesai);

            // overlap dengan bulan yang ditampilkan
            $start = $pStart->greaterThan($startOfMonth) ? $pStart->copy() : $startOfMonth->copy();
            $end   = $pEnd->lessThan($endOfMonth) ? $pEnd->copy() : $endOfMonth->copy();

            if ($start->gt($end)) {
                continue;
            }

            // peserta untuk pendaftaran ini:
            // jika ada anggota terdaftar (members_count > 0) gunakan itu, kalau tidak anggap 1
            $participants = max(1, (int) $p->members_count);

            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $key = $d->toDateString();
                if (! isset($calendarGlobal[$key])) {
                    $calendarGlobal[$key] = 0;
                }
                $calendarGlobal[$key] += $participants;
            }
        }

        // ---------- DATA KHUSUS USER: cek apakah user terlibat (pemilik atau anggota tim) ----------
        $userActiveDates = [];
        if (auth()->check()) {
            $userId = auth()->id();

            $userPends = PendaftaranMagang::withCount('members')
                ->whereIn('status_verifikasi', $validStatuses)
                ->where(function ($q) use ($userId) {
                    // pemilik pendaftaran
                    $q->where('user_id', $userId)
                      // atau user adalah anggota tim (relasi members menyimpan user_id atau sejenis)
                      ->orWhereHas('members', function ($q2) use ($userId) {
                          $q2->where('user_id', $userId);
                      });
                })
                ->whereDate('tanggal_mulai', '<=', $endOfMonth->toDateString())
                ->whereDate('tanggal_selesai', '>=', $startOfMonth->toDateString())
                ->get();

            foreach ($userPends as $p) {
                $pStart = Carbon::parse($p->tanggal_mulai);
                $pEnd   = Carbon::parse($p->tanggal_selesai);

                $start = $pStart->greaterThan($startOfMonth) ? $pStart->copy() : $startOfMonth->copy();
                $end   = $pEnd->lessThan($endOfMonth) ? $pEnd->copy() : $endOfMonth->copy();

                if ($start->gt($end)) continue;

                for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                    $userActiveDates[$d->toDateString()] = true;
                }
            }
        }

        // OPTIONAL: sort calendarGlobal by key agar predictable
        ksort($calendarGlobal);

        // -- debug sementara (uncomment jika butuh) --
        // \Log::debug('calendarGlobal', $calendarGlobal);
        // dd($calendarGlobal);

        return view('dashboard', [
            'stats'            => $stats,
            'pendaftaranTerbaru'=> $pendaftaranTerbaru,
            'calendarGlobal'   => $calendarGlobal,
            'userActiveDates'  => $userActiveDates,
            'currentMonth'     => $currentMonth,
            'startOfCalendar'  => $startOfCalendar,
            'endOfCalendar'    => $endOfCalendar,
        ]);
    }
}
