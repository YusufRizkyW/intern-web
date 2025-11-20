<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendaftaranMagang;
use App\Models\KuotaMagang;
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

        // ---------- AMBIL DATA KUOTA BULAN INI ----------
        $year  = $currentMonth->year;
        $month = $currentMonth->month;

        // ambil kuota bulan ini
        $kuota = KuotaMagang::where('tahun', $year)
            ->where('bulan', $month)
            ->where('is_active', true)
            ->first();

        $kuota_bulan  = $kuota ? $kuota->kuota_maksimal : null;
        $kuota_terisi = $kuota ? $kuota->kuota_terisi : 0;
        $kuota_sisa   = $kuota ? max(0, $kuota->kuota_maksimal - $kuota->kuota_terisi) : null;

        // ---------- AMBIL KUOTA TERSEDIA UNTUK DASHBOARD ----------
        $kuotaTersedia = $this->getKuotaTersedia();

        // ---------- PREPARE DATA UNTUK VIEW ----------
        $data = [
            'stats'             => $stats,
            'pendaftaranTerbaru' => $pendaftaranTerbaru,
            'calendarGlobal'    => $calendarGlobal,
            'userActiveDates'   => $userActiveDates,
            'currentMonth'      => $currentMonth,
            'startOfCalendar'   => $startOfCalendar,
            'endOfCalendar'     => $endOfCalendar,
            'kuota_bulan'       => $kuota_bulan,
            'kuota_terisi'      => $kuota_terisi,
            'kuota_sisa'        => $kuota_sisa,
            'kuotaTersedia'     => $kuotaTersedia, // ✅ Tambahan untuk dashboard
        ];

        // jika request AJAX (fetch dari JS), kembalikan hanya partial calendar
        if ($request->ajax() || $request->wantsJson()) {
            // partial view hanya berisi isi kalender (grid + legend + header)
            return view('dashboard._calendar', $data)->render();
        }

        // default: render halaman penuh
        return view('dashboard', $data);
    }

    /**
     * Ambil data kuota tersedia untuk beberapa bulan ke depan
     */
    private function getKuotaTersedia()
    {
        $currentDate = Carbon::now();
        $endDate = $currentDate->copy()->addMonths(6); // 6 bulan ke depan

        return KuotaMagang::where(function ($query) use ($currentDate, $endDate) {
            $query->where(function ($q) use ($currentDate) {
                // Bulan ini atau setelahnya
                $q->where('tahun', '>', $currentDate->year)
                  ->orWhere(function ($qq) use ($currentDate) {
                      $qq->where('tahun', '=', $currentDate->year)
                         ->where('bulan', '>=', $currentDate->month);
                  });
            })
            ->where(function ($q) use ($endDate) {
                // Maksimal 6 bulan ke depan
                $q->where('tahun', '<', $endDate->year)
                  ->orWhere(function ($qq) use ($endDate) {
                      $qq->where('tahun', '=', $endDate->year)
                         ->where('bulan', '<=', $endDate->month);
                  });
            });
        })
        ->orderBy('tahun', 'asc')
        ->orderBy('bulan', 'asc')
        ->limit(8) // Maksimal 8 card untuk menghindari terlalu penuh
        ->get();
    }
}
