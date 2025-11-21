<x-app-layout>
    @php
        // fallback kalau controller / route belum ngirim data
        $stats = $stats ?? [
            'total_pendaftar' => 0,
            'sedang_diproses' => 0,
            'selesai' => 0,
            'pendaftar' => 0,
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
                            dan melihat riwayat pendaftaran anda.
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
                                    {{ __('Masuk') }}
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                       class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded bg-red-600 text-white hover:bg-red-700">
                                        {{ __('Registrasi') }}
                                    </a>
                                @endif
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- SECTION 1.25: KALENDER --}}
            {{-- masukkan partial --}}
            @include('dashboard._calendar') 
            {{-- END kalender --}}

            {{-- SECTION 1.5: STATISTIK SINGKAT --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">Total Pendaftar</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ $stats['pendaftar'] ?? 0 }}
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
                            Isi formulir pendaftaran untuk magang secara individu maupun tim.
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
                            atau sudah aktif.
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
            {{-- <div class="bg-white shadow-sm rounded-lg p-6">
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
                                Periode:
                                <span class="font-semibold">
                                    {{ \Carbon\Carbon::parse($pendaftaranTerbaru->tanggal_mulai)->format('d M Y') }}
                                    –
                                    {{ \Carbon\Carbon::parse($pendaftaranTerbaru->tanggal_selesai)->format('d M Y') }}
                                </span>
                            </p>
                            <p class="flex items-center gap-2">
                                Status:
                                <span class="px-2 py-1 rounded text-[11px] font-semibold {{ $badgeClass }}">
                                    {{ ucfirst($status) }}
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
            </div> --}}

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
                                Pantau secara berkala status pendaftaran di menu "Status Pendaftaran".
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
                    <li>Pastikan link Google Drive dapat diakses secara publik.</li>
                    <li>Cek status pendaftaran secara berkala.</li>
                    <li>Jika ada kendala, hubungi admin melalui email resmi (isi sesuai instansi kamu).</li>
                </ul>
            </div>


        </div>
    </div>

@vite('resources/js/dashboard.js')

</x-app-layout>
