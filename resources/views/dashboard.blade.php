<x-app-layout>
    @php
        // fallback kalau controller / route belum ngirim data
        $stats = $stats ?? [
            'total_pendaftar' => 0,
            'sedang_diproses' => 0,
            'selesai' => 0,
        ];

        $pendaftaranTerbaru = $pendaftaranTerbaru ?? null;
    @endphp

    @php
        $stats = $stats ?? [
            'total_pendaftar' => 0,
            'sedang_diproses' => 0,
            'selesai' => 0,
        ];

        $pendaftaranTerbaru = $pendaftaranTerbaru ?? null;
        $calendarGlobal   = $calendarGlobal ?? [];
        $userActiveDates  = $userActiveDates ?? [];
        $currentMonth     = $currentMonth ?? \Carbon\Carbon::now()->startOfMonth();
        $startOfCalendar  = $startOfCalendar ?? $currentMonth->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::MONDAY);
        $endOfCalendar    = $endOfCalendar ?? $currentMonth->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);
    @endphp

    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Magang') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- SECTION 1: HERO / WELCOME --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="space-y-2">
                        <h1 class="text-2xl font-bold text-gray-800">
                            Selamat datang di Portal Magang
                        </h1>
                        <p class="text-sm text-gray-600">
                            Di sini kamu bisa mengajukan pendaftaran magang, memantau status pendaftaran,
                            dan melihat riwayat magang yang sudah selesai.
                        </p>

                        @guest
                            <p class="text-xs text-gray-500">
                                Untuk mengajukan pendaftaran, silakan login atau registrasi terlebih dahulu.
                            </p>
                        @endguest
                    </div>

                    <div class="flex flex-col items-stretch gap-2 md:items-end">
                        @auth
                            <div class="text-right text-sm text-gray-700">
                                <div class="font-semibold">
                                    Halo, {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>
                        @else
                            <div class="flex flex-wrap gap-2 justify-start md:justify-end">
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                    Login
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                       class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded bg-red-600 text-white hover:bg-red-700">
                                        Registrasi
                                    </a>
                                @endif
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- SECTION 3.5: KALENDER MAGANG --}}
            <div class="bg-white shadow-sm rounded-lg p-6 md:p-8">
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

                    <div class="text-xs text-gray-600">
                        {{ $currentMonth->translatedFormat('F Y') }}
                    </div>
                </div>

                {{-- Header hari --}}
                <div class="mt-4 grid grid-cols-7 text-center text-[11px] font-semibold text-gray-500">
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div>Sab</div>
                    <div>Min</div>
                </div>

                {{-- Grid tanggal --}}
                <div class="mt-1 grid grid-cols-7 gap-1 text-xs">
                    @php
                        $date = $startOfCalendar->copy();
                    @endphp

                    @while ($date->lte($endOfCalendar))
                        @php
                            $key = $date->toDateString();
                            $count = $calendarGlobal[$key] ?? 0;
                            $isCurrentMonth = $date->month === $currentMonth->month;
                            $isUserDay = !empty($userActiveDates[$key]);

                            // Warna dasar berdasarkan jumlah peserta aktif
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

                            // Highlight periode magang user
                            if ($isUserDay) {
                                $extraClass .= ' ring-2 ring-red-500 ring-offset-1';
                            }
                        @endphp

                        <div class="h-12 flex flex-col items-center justify-center rounded {{ $bgClass }} {{ $extraClass }}">
                            <span class="text-[11px] font-semibold">
                                {{ $date->day }}
                            </span>
                            @if ($count > 0)
                                <span class="text-[10px]">
                                    {{ $count }} org
                                </span>
                            @endif
                        </div>

                        @php
                            $date->addDay();
                        @endphp
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

            {{-- SECTION 1.5: STATISTIK SINGKAT --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">Total Pendaftar</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ $stats['total_pendaftar'] ?? 0 }}
                    </p>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">Sedang Diproses</p>
                    <p class="mt-2 text-2xl font-bold text-yellow-600">
                        {{ $stats['sedang_diproses'] ?? 0 }}
                    </p>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">Magang Selesai</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600">
                        {{ $stats['selesai'] ?? 0 }}
                    </p>
                </div>
            </div>

            {{-- SECTION 2: QUICK ACTIONS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Kartu "Ajukan Pendaftaran" --}}
                <div class="bg-white shadow-sm rounded-lg p-6 flex flex-col justify-between">
                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold text-gray-800">
                            Ajukan Pendaftaran Magang
                        </h3>
                        <p class="text-xs text-gray-600">
                            Isi formulir pendaftaran untuk magang secara individu maupun tim/rombongan.
                        </p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('pendaftaran.create') }}"
                           class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded bg-red-600 text-white hover:bg-red-700">
                            Buka Form Pendaftaran
                        </a>
                    </div>
                </div>

                {{-- Kartu "Status Pendaftaran" --}}
                <div class="bg-white shadow-sm rounded-lg p-6 flex flex-col justify-between">
                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold text-gray-800">
                            Status Pendaftaran
                        </h3>
                        <p class="text-xs text-gray-600">
                            Lihat apakah pendaftaran kamu masih pending, perlu revisi, sudah diterima,
                            atau sudah aktif / selesai.
                        </p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('pendaftaran.status') }}"
                           class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Cek Status
                        </a>
                    </div>
                </div>

                {{-- Kartu "Riwayat Magang" --}}
                <div class="bg-white shadow-sm rounded-lg p-6 flex flex-col justify-between">
                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold text-gray-800">
                            Riwayat Magang
                        </h3>
                        <p class="text-xs text-gray-600">
                            Lihat daftar magang yang sudah kamu jalani beserta catatan admin dan sertifikat (jika ada).
                        </p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('riwayat.user.index') }}"
                           class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>

            {{-- SECTION 2.5: RINGKASAN PENDAFTARAN TERBARU USER --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                @auth
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">
                        Ringkasan Pendaftaran Terbaru Saya
                    </h3>

                    @if ($pendaftaranTerbaru)
                        @php
                            $status = $pendaftaranTerbaru->status;
                            $badgeClass = match ($status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'revisi' => 'bg-red-100 text-red-800',
                                'diterima', 'aktif' => 'bg-blue-100 text-blue-800',
                                'selesai' => 'bg-emerald-100 text-emerald-800',
                                'batal', 'arsip' => 'bg-gray-100 text-gray-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp

                        <div class="text-xs text-gray-700 space-y-1">
                            <p>
                                Instansi:
                                <span class="font-semibold">
                                    {{ $pendaftaranTerbaru->instansi->nama ?? '-' }}
                                </span>
                            </p>
                            <p>
                                Periode:
                                <span class="font-semibold">
                                    {{ \Carbon\Carbon::parse($pendaftaranTerbaru->tanggal_mulai)->format('d M Y') }}
                                    –
                                    {{ \Carbon\Carbon::parse($pendaftaranTerbaru->tanggal_selesai)->format('d M Y') }}
                                </span>
                            </p>
                            <p class="flex items-center gap-2">
                                Status:
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-semibold {{ $badgeClass }}">
                                    {{ strtoupper($status) }}
                                </span>
                            </p>
                            <p class="text-[11px] text-gray-500">
                                Untuk detail lengkap, silakan cek di halaman Status Pendaftaran atau Riwayat Magang.
                            </p>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('pendaftaran.status') }}"
                               class="inline-flex items-center px-3 py-1.5 text-[11px] font-semibold rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Buka Halaman Status
                            </a>
                            <a href="{{ route('riwayat.user.index') }}"
                               class="inline-flex items-center px-3 py-1.5 text-[11px] font-semibold rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Lihat Riwayat Lengkap
                            </a>
                        </div>
                    @else
                        <p class="text-xs text-gray-600 mb-2">
                            Kamu belum memiliki pendaftaran magang yang tercatat di sistem.
                        </p>
                        <a href="{{ route('pendaftaran.create') }}"
                           class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded bg-red-600 text-white hover:bg-red-700">
                            Ajukan Pendaftaran Pertama
                        </a>
                    @endif
                @else
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">
                        Ingin melihat status pendaftaran kamu?
                    </h3>
                    <p class="text-xs text-gray-600 mb-3">
                        Silakan login terlebih dahulu untuk mengakses status pengajuan dan riwayat magang.
                    </p>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Login untuk melihat status
                    </a>
                @endauth
            </div>

            {{-- SECTION 3: ALUR SINGKAT --}}
            <div class="bg-white shadow-sm rounded-lg p-6 md:p-8">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">
                    Alur Singkat Pendaftaran Magang
                </h3>

                <ol class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs text-gray-700">
                    <li class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center text-xs font-bold">
                            1
                        </div>
                        <div>
                            <div class="font-semibold">Buat Akun / Login</div>
                            <div class="text-[11px] text-gray-500">
                                Registrasi atau login ke portal ini.
                            </div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center text-xs font-bold">
                            2
                        </div>
                        <div>
                            <div class="font-semibold">Isi Form Pendaftaran</div>
                            <div class="text-[11px] text-gray-500">
                                Pilih individu / tim, isi data peserta dan periode magang.
                            </div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center text-xs font-bold">
                            3
                        </div>
                        <div>
                            <div class="font-semibold">Kirim Link Google Drive</div>
                            <div class="text-[11px] text-gray-500">
                                Upload berkas (CV, surat pengantar, KTM, dll) ke Drive dan kirim link-nya.
                            </div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center text-xs font-bold">
                            4
                        </div>
                        <div>
                            <div class="font-semibold">Tunggu Verifikasi Admin</div>
                            <div class="text-[11px] text-gray-500">
                                Pantau status pendaftaran di menu "Status Pendaftaran".
                            </div>
                        </div>
                    </li>
                </ol>
            </div>

            {{-- SECTION 4: INFO PENTING --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">
                    Informasi Penting
                </h3>
                <ul class="list-disc pl-5 text-xs text-gray-600 space-y-1">
                    <li>Pastikan link Google Drive dapat diakses tanpa login (Anyone with the link).</li>
                    <li>Gunakan email yang aktif karena update pendaftaran akan dikirim ke email tersebut.</li>
                    <li>Jika ada kendala, hubungi admin melalui email resmi (isi sesuai instansi kamu).</li>
                </ul>
            </div>


        </div>
    </div>
</x-app-layout>
