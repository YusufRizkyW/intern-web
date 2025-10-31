<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Status Pendaftaran Magang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Card utama --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (!$pendaftaran)
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
                        </div>
                    @else
                        {{-- Bagian ringkasan status --}}
                        <div class="mb-6">
                            <h1 class="text-lg font-semibold text-gray-800 mb-2">
                                Halo, {{ $pendaftaran->nama_lengkap }}
                            </h1>

                            <div class="text-sm text-gray-600 mb-4">
                                Berikut status pendaftaran magang Anda.
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div class="border rounded p-4 bg-gray-50">
                                    <div class="text-xs text-gray-500">Status Verifikasi</div>

                                    @php
                                        $status = $pendaftaran->status_verifikasi;
                                        $statusLabel = [
                                            'pending'  => 'Menunggu Review',
                                            'revisi'   => 'Perlu Revisi',
                                            'diterima' => 'Diterima',
                                            'ditolak'  => 'Ditolak',
                                        ][$status] ?? $status;

                                        $statusColor = match($status) {
                                            'pending'  => 'bg-yellow-100 text-yellow-800',
                                            'revisi'   => 'bg-blue-100 text-blue-800',
                                            'diterima' => 'bg-green-100 text-green-800',
                                            'ditolak'  => 'bg-red-100 text-red-800',
                                            default    => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp

                                    <span class="inline-block text-xs font-semibold px-2 py-1 rounded {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="border rounded p-4 bg-gray-50">
                                    <div class="text-xs text-gray-500">Periode Magang</div>

                                    @if ($pendaftaran->tipe_periode === 'durasi')
                                        <div class="text-sm font-medium text-gray-800">
                                            {{ $pendaftaran->durasi_bulan }} bulan
                                        </div>
                                    @else
                                        <div class="text-sm font-medium text-gray-800">
                                            {{ \Carbon\Carbon::parse($pendaftaran->tanggal_mulai)->format('d M Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($pendaftaran->tanggal_selesai)->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                        {{-- Data pendaftar --}}
                        <div class="mb-8">
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
                            </div>
                        </div>

                        {{-- Berkas yang diunggah --}}
                        <div class="mb-4">
                            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                                Dokumen Persyaratan
                            </h2>

                            <div class="overflow-x-auto border rounded">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-100 text-left text-gray-600 text-xs uppercase">
                                        <tr>
                                            <th class="px-4 py-2">Jenis Berkas</th>
                                            <th class="px-4 py-2">Status Dokumen</th>
                                            <th class="px-4 py-2">Catatan Admin</th>
                                            <th class="px-4 py-2">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach ($berkas as $item)
                                            @php
                                                $dokColor = match($item->valid) {
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'valid'   => 'bg-green-100 text-green-800',
                                                    'invalid' => 'bg-red-100 text-red-800',
                                                    default   => 'bg-gray-100 text-gray-800',
                                                };

                                                $dokLabel = [
                                                    'pending' => 'Menunggu',
                                                    'valid'   => 'Valid',
                                                    'invalid' => 'Perlu Revisi',
                                                ][$item->valid] ?? $item->valid;
                                            @endphp

                                            <tr class="align-top">
                                                <td class="px-4 py-3 font-medium text-gray-800">
                                                    {{ strtoupper(str_replace('_', ' ', $item->jenis_berkas)) }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    <span class="inline-block text-xs font-semibold px-2 py-1 rounded {{ $dokColor }}">
                                                        {{ $dokLabel }}
                                                    </span>
                                                </td>

                                                <td class="px-4 py-3 text-gray-700 text-xs">
                                                    @if ($item->valid === 'invalid')
                                                        {{ $item->catatan_admin ?: 'Silakan upload ulang dokumen ini.' }}
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>

                                                <td class="px-4 py-3 text-xs text-gray-700">
                                                    @if ($item->valid === 'invalid')
                                                        <form
                                                            action="{{ route('pendaftaran.reupload', $item->id) }}"
                                                            method="POST"
                                                            enctype="multipart/form-data"
                                                            class="space-y-2"
                                                        >
                                                            @csrf

                                                            <input
                                                                type="file"
                                                                name="file_baru"
                                                                accept="application/pdf"
                                                                class="block w-full text-xs border rounded p-1"
                                                                required
                                                            >

                                                            <button
                                                                type="submit"
                                                                class="bg-red-600 text-white text-xs font-semibold px-3 py-1 rounded hover:bg-red-700"
                                                            >
                                                                Upload Ulang
                                                            </button>

                                                            <p class="text-[10px] text-gray-500">
                                                                PDF saja, maks 2MB.
                                                            </p>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- pesan bantuan kalau ada revisi --}}
                            @if ($pendaftaran->status_verifikasi === 'revisi')
                                <div class="mt-4 p-3 bg-blue-50 text-blue-700 text-xs rounded">
                                    Beberapa dokumen perlu diperbaiki. Silakan upload ulang dokumen yang statusnya
                                    <span class="font-semibold">"Perlu Revisi"</span>.
                                </div>
                            @endif
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
    