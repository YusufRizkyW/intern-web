{{-- resources/views/dashboard/_calendar.blade.php --}}
<div id="calendar-container">
    <div class="bg-white shadow-sm rounded-lg p-6 md:p-8">
        {{-- Header with prev/next --}}
        @php
            $cur = $currentMonth instanceof \Carbon\Carbon ? $currentMonth : \Carbon\Carbon::parse($currentMonth);
            $prev = $cur->copy()->subMonth()->format('Y-m');
            $next = $cur->copy()->addMonth()->format('Y-m');
            $baseUrl = request()->url(); // will be same path
            $prevUrl = $baseUrl . '?month=' . $prev;
            $nextUrl = $baseUrl . '?month=' . $next;
        @endphp

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">
                    Kalender Kepadatan Magang
                </h3>
                @auth
                    <p class="text-[11px] text-gray-500 mt-1">
                        Warna menunjukkan jumlah total peserta magang aktif per hari.
                        Tanggal dengan <span class="font-semibold text-red-600">garis merah</span> adalah
                        periode magang kamu sendiri.
                    </p>
                @else
                    <p class="text-[11px] text-gray-500 mt-1">
                        Warna menunjukkan jumlah total peserta magang aktif per hari
                        pada bulan ini.
                    </p>
                @endauth
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ $prevUrl }}"
                   class="calendar-nav inline-flex items-center px-2 py-1 bg-white border rounded shadow-sm text-sm hover:bg-gray-50"
                   aria-label="Bulan sebelumnya">
                    &lt;
                </a>

                <div class="text-sm font-medium text-gray-700">
                    {{ $cur->translatedFormat('F Y') }}
                </div>

                <a href="{{ $nextUrl }}"
                   class="calendar-nav inline-flex items-center px-2 py-1 bg-white border rounded shadow-sm text-sm hover:bg-gray-50"
                   aria-label="Bulan berikutnya">
                    &gt;
                </a>
            </div>
        </div>

        {{-- Header hari --}}
        <div class="mt-4 grid grid-cols-7 text-center text-[11px] font-semibold text-gray-500">
            <div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div><div>Min</div>
        </div>

        {{-- Grid tanggal --}}
        <div class="mt-1 grid grid-cols-7 gap-1 text-xs">
            @php $date = $startOfCalendar->copy(); @endphp

            @while ($date->lte($endOfCalendar))
                @php
                    $key = $date->toDateString();
                    $count = $calendarGlobal[$key] ?? 0;
                    $isCurrentMonth = $date->month === $currentMonth->month;
                    $isUserDay = !empty($userActiveDates[$key]);

                    if ($count === 0) {
                        $bgClass = 'bg-gray-50 text-gray-400';
                    } elseif ($count <= 3) {
                        $bgClass = 'bg-emerald-100 text-emerald-800';
                    } elseif ($count <= 7) {
                        $bgClass = 'bg-yellow-100 text-yellow-800';
                    } else {
                        $bgClass = 'bg-red-100 text-red-800';
                    }

                    $extraClass = $isCurrentMonth ? '' : 'opacity-40';
                    if ($isUserDay) $extraClass .= ' ring-2 ring-red-500 ring-offset-1';
                @endphp

                <div class="h-12 flex flex-col items-center justify-center rounded {{ $bgClass }} {{ $extraClass }}">
                    <span class="text-[11px] font-semibold">{{ $date->day }}</span>
                    @if ($count > 0)
                        <span class="text-[10px]">{{ $count }} org</span>
                    @endif
                </div>

                @php $date->addDay(); @endphp
            @endwhile
        </div>

        {{-- Legend kecil --}}
        <div class="mt-4 flex flex-wrap gap-3 text-[11px] text-gray-600">
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded bg-gray-50 border border-gray-200"></span>
                <span>0 peserta</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded bg-emerald-100"></span>
                <span>1–3 peserta</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded bg-yellow-100"></span>
                <span>4–7 peserta</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded bg-red-100"></span>
                <span>≥ 8 peserta</span>
            </div>
            @auth
                <div class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded border border-red-500"></span>
                    <span>Periode magang kamu</span>
                </div>
            @endauth
        </div>
    </div>
</div>
