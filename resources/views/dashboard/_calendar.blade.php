{{-- resources/views/dashboard/_calendar.blade.php --}}
<div id="calendar-container">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        {{-- Header with prev/next --}}
        @php
            $cur = $currentMonth instanceof \Carbon\Carbon ? $currentMonth : \Carbon\Carbon::parse($currentMonth);
            $prev = $cur->copy()->subMonth()->format('Y-m');
            $next = $cur->copy()->addMonth()->format('Y-m');
            $baseUrl = request()->url(); // will be same path
            $prevUrl = $baseUrl . '?month=' . $prev;
            $nextUrl = $baseUrl . '?month=' . $next;
        @endphp

        {{-- Header Section --}}
        <div class="bg-[#052d57] p-6 border-b border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m12-10v10M5 7h14"/>
                        </svg>
                        Kalender Kepadatan Magang
                    </h3>
                    @auth
                        <p class="text-sm text-gray-200 mt-2">
                            Warna menunjukkan jumlah total peserta magang aktif per hari.
                            <span class="inline-flex items-center gap-1 font-medium text-red-300">
                                <span class="w-2 h-2 rounded-full border-2 border-red-400"></span>
                                Periode magang Anda
                            </span>
                        </p>
                    @else
                        <p class="text-sm text-gray-200 mt-2">
                            Warna menunjukkan jumlah total peserta magang aktif per hari pada bulan ini.
                        </p>
                    @endauth
                </div>

                {{-- Navigation Controls --}}
                <div class="flex items-center gap-3">
                    <a href="{{ $prevUrl }}"
                       class="calendar-nav inline-flex items-center justify-center w-10 h-10 bg-white/10 border-2 border-white/20 rounded-lg shadow-sm text-white hover:bg-white/20 hover:border-white/30 transition-all duration-200"
                       aria-label="Bulan sebelumnya">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>

                    <div class="px-4 py-2 bg-white/10 rounded-lg border-2 border-white/20 shadow-sm backdrop-blur-sm">
                        <div class="text-lg font-bold text-white text-center min-w-[120px]">
                            {{ $cur->translatedFormat('F Y') }}
                        </div>
                    </div>

                    <a href="{{ $nextUrl }}"
                       class="calendar-nav inline-flex items-center justify-center w-10 h-10 bg-white/10 border-2 border-white/20 rounded-lg shadow-sm text-white hover:bg-white/20 hover:border-white/30 transition-all duration-200"
                       aria-label="Bulan berikutnya">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Kuota Info --}}
            <div class="mt-4 flex items-center justify-between p-4 bg-white/10 rounded-lg border border-white/20 backdrop-blur-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-200">Kuota bulan ini:</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-lg font-bold text-white">
                        {{ $kuota_bulan !== null ? $kuota_bulan : '-' }}
                    </span>
                    @if($kuota_bulan !== null)
                        <div class="flex items-center gap-1">
                            <span class="text-sm text-gray-300">(</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $kuota_sisa > 10 ? 'bg-green-400/20 text-green-300 border border-green-400/30' : ($kuota_sisa > 5 ? 'bg-yellow-400/20 text-yellow-300 border border-yellow-400/30' : 'bg-red-400/20 text-red-300 border border-red-400/30') }}">
                                sisa {{ $kuota_sisa }}
                            </span>
                            <span class="text-sm text-gray-300">)</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Calendar Content --}}
        <div class="p-6">
            {{-- Header hari dengan styling yang lebih baik --}}
            <div class="grid grid-cols-7 mb-3">
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                    <div class="text-center py-2 text-xs font-bold text-gray-600 uppercase tracking-wide">
                        {{ substr($day, 0, 3) }}
                    </div>
                @endforeach
            </div>

            {{-- Grid tanggal dengan styling yang diperbaiki --}}
            <div class="grid grid-cols-7 gap-2">
                @php $date = $startOfCalendar->copy(); @endphp

                @while ($date->lte($endOfCalendar))
                    @php
                        $key = $date->toDateString();
                        $count = $calendarGlobal[$key] ?? 0;
                        $isCurrentMonth = $date->month === $currentMonth->month;
                        $isUserDay = !empty($userActiveDates[$key]);
                        $isToday = $date->isToday();

                        if ($count === 0) {
                            $bgClass = 'bg-gray-50 text-gray-500 border-gray-200';
                        } elseif ($count <= 3) {
                            $bgClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        } elseif ($count <= 7) {
                            $bgClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                        } else {
                            $bgClass = 'bg-red-50 text-red-700 border-red-200';
                        }

                        $extraClass = $isCurrentMonth ? 'border-2' : 'opacity-40 border';
                        
                        if ($isToday && $isCurrentMonth) {
                            $extraClass .= ' ring-2 ring-blue-400 ring-offset-2';
                        }
                        
                        if ($isUserDay) {
                            $extraClass .= ' ring-2 ring-red-500 ring-offset-1';
                        }
                    @endphp

                    <div class="relative h-16 flex flex-col items-center justify-center rounded-lg transition-all duration-200 hover:scale-105 {{ $bgClass }} {{ $extraClass }}">
                        <div class="text-sm font-bold">{{ $date->day }}</div>
                        @if ($count > 0)
                            <div class="text-xs font-medium opacity-75">{{ $count }} psrt</div>
                        @endif
                        
                        {{-- Today indicator --}}
                        @if ($isToday && $isCurrentMonth)
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full"></div>
                        @endif
                    </div>

                    @php $date->addDay(); @endphp
                @endwhile
            </div>

            {{-- Legend dengan styling yang lebih menarik --}}
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="text-xs font-semibold text-gray-700 mb-3">Keterangan:</div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded border-2 border-gray-200 bg-gray-50"></span>
                        <span class="text-gray-600">0 peserta</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded border-2 border-emerald-200 bg-emerald-50"></span>
                        <span class="text-gray-600">1–3 peserta</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded border-2 border-yellow-200 bg-yellow-50"></span>
                        <span class="text-gray-600">4–7 peserta</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded border-2 border-red-200 bg-red-50"></span>
                        <span class="text-gray-600">≥ 8 peserta</span>
                    </div>
                    @auth
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded border-2 border-red-500 bg-white ring-2 ring-red-500 ring-offset-1"></span>
                            <span class="text-gray-600 font-medium">Periode Anda</span>
                        </div>
                    @endauth
                </div>
                
                {{-- Additional info --}}
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span>Hari ini</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS untuk smooth transitions --}}
<style>
    .calendar-nav:hover {
        transform: translateY(-1px);
    }
    
    .grid > div:hover {
        z-index: 10;
    }
</style>
