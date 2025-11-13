<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\PendaftaranMagang;
use Carbon\Carbon;

class CalendarMagangWidget extends Widget
{
    // Judul widget di Filament
    protected static ?string $heading = 'Kalender Kepadatan Magang';

    // view blade untuk widget
    protected static string $view = 'filament.widgets.calendar-magang-widget';

    // public properties untuk Livewire reactivity
    public string $month; // format Y-m, ex: "2025-11"
    public array $calendarGlobal = [];
    public array $userActiveDates = [];

    public function mount(): void
    {
        // inisialisasi bulan (ambil dari query atau default sekarang)
        $this->month = request()->query('month', now()->format('Y-m'));
        $this->loadData();
    }

    // method yang memuat data kalender (panggil ulang saat prev/next)
    public function loadData(): void
    {
        $currentMonth = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth   = $currentMonth->copy()->endOfMonth();

        $validStatuses = ['aktif']; // sesuaikan

        $pendaftaranGlobal = PendaftaranMagang::withCount('members')
            ->whereIn('status_verifikasi', $validStatuses)
            ->whereDate('tanggal_mulai', '<=', $endOfMonth->toDateString())
            ->whereDate('tanggal_selesai', '>=', $startOfMonth->toDateString())
            ->get();

        $calendar = [];
        foreach ($pendaftaranGlobal as $p) {
            $pStart = Carbon::parse($p->tanggal_mulai);
            $pEnd   = Carbon::parse($p->tanggal_selesai);

            $start = $pStart->greaterThan($startOfMonth) ? $pStart->copy() : $startOfMonth->copy();
            $end   = $pEnd->lessThan($endOfMonth) ? $pEnd->copy() : $endOfMonth->copy();

            if ($start->gt($end)) continue;

            $participants = max(1, (int) $p->members_count);

            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $key = $d->toDateString();
                $calendar[$key] = ($calendar[$key] ?? 0) + $participants;
            }
        }

        // userActiveDates: cek jika admin ingin melihat juga dirinya (opsional)
        $userActive = [];
        // Jika ingin highlight admin sendiri, kamu bisa menyesuaikan; biasanya admin tidak dimark
        // jika mau highlight staff tertentu, tambahkan logika here.

        // set properties (Livewire akan re-render)
        $this->calendarGlobal = $calendar;
        $this->userActiveDates = $userActive;
    }

    // aksi prev/next dipanggil dari blade via wire:click
    public function prevMonth(): void
    {
        $dt = Carbon::createFromFormat('Y-m', $this->month)->subMonth();
        $this->month = $dt->format('Y-m');
        $this->loadData();
    }

    public function nextMonth(): void
    {
        $dt = Carbon::createFromFormat('Y-m', $this->month)->addMonth();
        $this->month = $dt->format('Y-m');
        $this->loadData();
    }
}
