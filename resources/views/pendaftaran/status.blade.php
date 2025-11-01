<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Status Pendaftaran Magang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- flash --}}
                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @php
                        // supaya gampang: status yang dianggap "aktif" / pendaftaran sedang berjalan
                        $statusAktif = ['pending', 'revisi', 'diterima', 'aktif'];
                        // status akhir => tampilan dikosongkan lagi
                        $statusFinal = ['selesai', 'batal', 'arsip'];
                    @endphp

                    {{-- KALAU TIDAK ADA PENDAFTARAN / SUDAH FINAL --}}
                    @if (!$pendaftaran || in_array($pendaftaran->status_verifikasi, $statusFinal, true))
                        <div class="text-center space-y-4 py-8">
                            <div class="text-lg font-semibold text-gray-800">
                                Anda belum mengirim pendaftaran magang.
                            </div>
                            <div class="text-sm text-gray-600">
                                Silakan ajukan pendaftaran terlebih dahulu.
                            </div>

                            <a href="{{ route('pendaftaran.create') }}"
                               class="inline-block bg-red-600 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-red-700">
                                Ajukan Pendaftaran
                            </a>

                            {{-- kalau mau, tampilkan riwayat terakhir --}}
                            @isset($riwayat_terbaru)
                                <div class="mt-6 p-3 bg-gray-50 rounded text-xs text-gray-600">
                                    Magang terakhir kamu:
                                    <strong>{{ $riwayat_terbaru->instansi ?? 'Instansi' }}</strong>
                                    @if ($riwayat_terbaru->tanggal_mulai && $riwayat_terbaru->tanggal_selesai)
                                        ({{ \Carbon\Carbon::parse($riwayat_terbaru->tanggal_mulai)->format('d M Y') }}
                                        –
                                        {{ \Carbon\Carbon::parse($riwayat_terbaru->tanggal_selesai)->format('d M Y') }})
                                    @endif
                                    — lihat di
                                    <a href="{{ route('riwayat.user.index') }}" class="text-red-600 underline">
                                        Riwayat Magang
                                    </a>.
                                </div>
                            @endisset
                        </div>


                    {{-- KALAU ADA PENDAFTARAN AKTIF --}}
                    @else
                        {{-- Bagian ringkasan status --}}
                        <div class="mb-6">
                            <h1 class="text-lg font-semibold text-gray-800 mb-2">
                                Halo, {{ $pendaftaran->nama_lengkap }}
                            </h1>
                            <p class="text-sm text-gray-600 mb-4">
                                Berikut status pendaftaran magang Anda.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Status verifikasi --}}
                                <div class="border rounded p-4 bg-gray-50">
                                    <div class="text-xs text-gray-500 mb-1">Status Verifikasi</div>
                                    @php
                                        $status = $pendaftaran->status_verifikasi;

                                        $statusLabel = [
                                            'pending'  => 'Menunggu Review',
                                            'revisi'   => 'Perlu Revisi',
                                            'diterima' => 'Diterima',
                                            'aktif'    => 'Aktif (Sedang Magang)',
                                            'ditolak'  => 'Ditolak',
                                            'selesai'  => 'Selesai',
                                            'batal'    => 'Dibatalkan',
                                            'arsip'    => 'Diarsipkan',
                                        ][$status] ?? $status;

                                        $statusColor = match ($status) {
                                            'pending'  => 'bg-yellow-100 text-yellow-800',
                                            'revisi'   => 'bg-blue-100 text-blue-800',
                                            'diterima' => 'bg-green-100 text-green-800',
                                            'aktif'    => 'bg-green-100 text-green-800',
                                            'ditolak'  => 'bg-red-100 text-red-800',
                                            default    => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-block text-xs font-semibold px-2 py-1 rounded {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                {{-- Periode --}}
                                <div class="border rounded p-4 bg-gray-50">
                                    <div class="text-xs text-gray-500 mb-1">Periode Magang</div>
                                    @if ($pendaftaran->tipe_periode === 'durasi')
                                        <div class="text-sm font-medium text-gray-800">
                                            {{ $pendaftaran->durasi_bulan }} bulan
                                        </div>
                                    @else
                                        <div class="text-sm font-medium text-gray-800">
                                            @if ($pendaftaran->tanggal_mulai && $pendaftaran->tanggal_selesai)
                                                {{ \Carbon\Carbon::parse($pendaftaran->tanggal_mulai)->format('d M Y') }}
                                                –
                                                {{ \Carbon\Carbon::parse($pendaftaran->tanggal_selesai)->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Data pendaftar --}}
                        <div class="mb-6">
                            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                                Data Pendaftar
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div class="border rounded p-4">
                                    <div class="text-gray-500 text-xs">Nama Lengkap</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->nama_lengkap }}</div>
                                </div>

                                <div class="border rounded p-4">
                                    <div class="text-gray-500 text-xs">NIM</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->nim ?: '-' }}</div>
                                </div>

                                <div class="border rounded p-4">
                                    <div class="text-gray-500 text-xs">Email</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->email }}</div>
                                </div>

                                <div class="border rounded p-4">
                                    <div class="text-gray-500 text-xs">No. HP / WA</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->no_hp ?: '-' }}</div>
                                </div>

                                <div class="border rounded p-4">
                                    <div class="text-gray-500 text-xs">Instansi / Agency Asal</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->agency ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Link Drive --}}
                        <div class="mb-6">
                            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                                Dokumen Persyaratan
                            </h2>

                            <p class="text-xs text-gray-500 mb-2">
                                Dokumen Anda disimpan di Google Drive. Admin akan memeriksa dokumen dari link berikut.
                            </p>

                            @if ($pendaftaran->link_drive)
                                <a href="{{ $pendaftaran->link_drive }}" target="_blank"
                                   class="inline-flex items-center gap-2 text-red-600 text-sm underline">
                                    Buka folder Google Drive
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M13.5 4.5H21m0 0v7.5m0-7.5L10.5 15 6 10.5 3 13.5" />
                                    </svg>
                                </a>
                            @else
                                <p class="text-xs text-gray-400">Belum ada link drive.</p>
                            @endif

                            @if ($pendaftaran->status_verifikasi === 'revisi')
                                <div class="mt-3 p-3 bg-blue-50 text-blue-700 text-xs rounded">
                                    Dokumen Anda perlu diperbaiki. Silakan perbarui berkas di folder Google Drive yang sudah Anda kirim,
                                    lalu hubungi admin jika diperlukan.
                                </div>
                            @endif
                        </div>

                        {{-- Catatan Admin --}}
                        @if ($pendaftaran->catatan_admin)
                            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
                                <div class="text-xs font-semibold text-yellow-700 mb-1">Catatan dari Admin</div>
                                <div class="text-sm text-gray-800">
                                    {{ $pendaftaran->catatan_admin }}
                                </div>
                            </div>
                        @endif

                        {{-- Info status tambahan --}}
                        @if ($pendaftaran->status_verifikasi === 'pending')
                            <p class="text-xs text-gray-500">
                                Pendaftaran Anda sedang menunggu verifikasi admin.
                            </p>
                        @elseif ($pendaftaran->status_verifikasi === 'diterima')
                            <p class="text-xs text-green-600">
                                Pendaftaran Anda sudah diterima. Silakan menunggu jadwal mulai / informasi selanjutnya.
                            </p>
                        @elseif ($pendaftaran->status_verifikasi === 'aktif')
                            <p class="text-xs text-green-600">
                                Anda sedang menjalani periode magang. Pastikan link Drive tetap bisa diakses.
                            </p>
                        @elseif ($pendaftaran->status_verifikasi === 'ditolak')
                            <p class="text-xs text-red-500">
                                Pendaftaran Anda ditolak. Silakan hubungi admin jika ingin mengajukan kembali.
                            </p>
                        @endif

                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
