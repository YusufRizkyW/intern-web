<x-filament-widgets::widget>
    <x-filament::section>
        <div>
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm text-gray-700 font-semibold">Kalender Kepadatan Magang</div>
                <div class="flex items-center gap-2">
                    <button wire:click="prevMonth" 
                            class="inline-flex items-center px-2 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50 text-gray-700 transition-colors">
                        ‹
                    </button>
                    <div class="text-sm font-medium min-w-[120px] text-center">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                    </div>
                    <button wire:click="nextMonth" 
                            class="inline-flex items-center px-2 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50 text-gray-700 transition-colors">
                        ›
                    </button>
                </div>
            </div>

            {{-- Header hari --}}
            <div class="grid grid-cols-7 text-center text-xs font-semibold text-gray-500 mb-2 py-2">
                <div>Sen</div>
                <div>Sel</div>
                <div>Rab</div>
                <div>Kam</div>
                <div>Jum</div>
                <div>Sab</div>
                <div>Min</div>
            </div>

            {{-- Calendar grid --}}
            @php
                $currentMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $startOfCalendar = $currentMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                $endOfCalendar = $currentMonth->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);
                $date = $startOfCalendar->copy();
            @endphp

            <div class="grid grid-cols-7 gap-1 text-xs">
                @while($date->lte($endOfCalendar))
                    @php
                        $key = $date->toDateString();
                        $count = $calendarGlobal[$key] ?? 0;
                        $isCurrentMonth = $date->month === $currentMonth->month;
                        
                        // Warna berdasarkan jumlah peserta
                        if ($count === 0) { 
                            $bg = 'bg-gray-50 text-gray-400 border-gray-200'; 
                        } elseif ($count <= 3) { 
                            $bg = 'bg-emerald-100 text-emerald-800 border-emerald-200'; 
                        } elseif ($count <= 7) { 
                            $bg = 'bg-yellow-100 text-yellow-800 border-yellow-200'; 
                        } else { 
                            $bg = 'bg-red-100 text-red-800 border-red-200'; 
                        }
                    @endphp

                    <div class="h-14 flex flex-col items-center justify-center rounded border {{ $bg }} {{ $isCurrentMonth ? '' : 'opacity-40' }} relative">
                        <div class="text-xs font-semibold">{{ $date->day }}</div>
                        @if($count > 0)
                            <div class="text-[10px] font-medium">{{ $count }} psrt</div>
                        @endif
                    </div>

                    @php $date->addDay(); @endphp
                @endwhile
            </div>

            {{-- Legend --}}
            <div class="flex items-center justify-center gap-4 mt-4 text-xs">
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-gray-50 border border-gray-200 rounded"></div>
                    <span class="text-gray-600">Kosong</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-emerald-100 border border-emerald-200 rounded"></div>
                    <span class="text-gray-600">1-3 psrt</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-yellow-100 border border-yellow-200 rounded"></div>
                    <span class="text-gray-600">4-7 psrt</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-red-100 border border-red-200 rounded"></div>
                    <span class="text-gray-600">8+ psrt</span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
