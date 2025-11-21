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
                        <div class="mb-4 p-3 bg-green-100 border border-green-200 text-green-700 rounded text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-100 border border-red-200 text-red-700 rounded text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @php
                        $statusFinal = ['selesai', 'batal', 'arsip'];
                    @endphp

                    {{-- TIDAK ADA PENDAFTARAN AKTIF / SUDAH FINAL --}}
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

                            @isset($riwayat_terbaru)
                                <div class="mt-6 p-3 bg-gray-50 rounded text-xs text-gray-600">
                                    Magang terakhir kamu:
                                    <strong>{{ $riwayat_terbaru->agency ?? 'Instansi' }}</strong>
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

                    {{-- ADA PENDAFTARAN AKTIF --}}
                    @else
                        {{-- Ringkasan status --}}
                        <div class="mb-6">
                            <h1 class="text-lg font-semibold text-gray-800 mb-1">
                                Halo, {{ $pendaftaran->nama_lengkap }}
                            </h1>

                            <p class="text-sm text-gray-600 mb-2">
                                Berikut status pendaftaran magang Anda.
                            </p>

                            <div class="mb-4">
                                <span class="inline-flex items-center gap-2 text-xs">
                                    <span class="font-semibold text-gray-600">Jenis pendaftaran:</span>
                                    @if ($pendaftaran->tipe_pendaftaran === 'tim')
                                        <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 font-semibold">
                                            Tim
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-gray-100 text-gray-800 font-semibold">
                                            Individu
                                        </span>
                                    @endif
                                </span>
                            </div>

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

                        {{-- Data pendaftar utama (individu atau ketua tim) --}}
                        <div class="mb-6">
                            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                                Data Peserta Utama
                                @if ($pendaftaran->tipe_pendaftaran === 'tim')
                                    <span class="text-[11px] text-gray-400 normal-case">(Ketua tim)</span>
                                @endif
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div class="border rounded p-4">
                                    <div class="text-gray-500 text-xs">Nama Lengkap</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->nama_lengkap }}</div>
                                </div>

                                <div class="border rounded p-4">
                                    <div class="text-gray-500 text-xs">NIM / NIS</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->nim ?: '-' }}</div>
                                </div>

                                <div class="border rounded p-4">
                                    <div class="text-gray-500 text-xs">Email</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->email ?: '-' }}</div>
                                </div>

                                <div class="border rounded p-4">
                                    <div class="text-gray-500 text-xs">No. HP / WA</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->no_hp ?: '-' }}</div>
                                </div>

                                <div class="border rounded p-4 md:col-span-2">
                                    <div class="text-gray-500 text-xs">Instansi / Asal</div>
                                    <div class="font-medium text-gray-800">{{ $pendaftaran->agency ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Anggota tim (kalau tipe tim) --}}
                        @if ($pendaftaran->tipe_pendaftaran === 'tim')
                            <div class="mb-6">
                                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-2">
                                    Anggota Tim
                                </h2>

                                @php
                                    $members = $pendaftaran->members ?? collect();
                                @endphp

                                @if ($members->isEmpty())
                                    <p class="text-xs text-gray-400">
                                        Tidak ada anggota tambahan yang diinput.
                                    </p>
                                @else
                                    <div class="overflow-x-auto border rounded">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-gray-100 text-xs text-gray-500 uppercase">
                                                <tr>
                                                    <th class="px-4 py-2 text-left">Nama</th>
                                                    <th class="px-4 py-2 text-left">NIM / NIS</th>
                                                    <th class="px-4 py-2 text-left">Email</th>
                                                    <th class="px-4 py-2 text-left">No HP</th>
                                                    <th class="px-4 py-2 text-left">Peran</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y">
                                                @foreach ($members as $member)
                                                    <tr>
                                                        <td class="px-4 py-2">
                                                            {{ $member->nama_anggota }}
                                                        </td>
                                                        <td class="px-4 py-2 text-gray-700">
                                                            {{ $member->nim_anggota ?: '-' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-gray-700">
                                                            {{ $member->email_anggota ?: '-' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-gray-700">
                                                            {{ $member->no_hp_anggota ?: '-' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-xs">
                                                            @if ($member->is_ketua)
                                                                <span class="inline-block px-2 py-1 rounded bg-blue-100 text-blue-800 font-semibold">
                                                                    Ketua
                                                                </span>
                                                            @else
                                                                <span class="text-gray-500">Anggota</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Link Drive --}}
                        <div class="mb-6">
                            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">
                                Dokumen Persyaratan
                            </h2>

                            <p class="text-xs text-gray-500 mb-2">
                                Dokumen disimpan di Google Drive. Admin akan memeriksa dokumen dari link berikut.
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
                                    Dokumen Anda perlu diperbaiki. Silakan perbarui berkas di folder
                                    Google Drive yang sudah Anda kirim, lalu pastikan link tetap bisa diakses.
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
                        @switch($pendaftaran->status_verifikasi)
                            @case('pending')
                                <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded">
                                    <div class="flex-1">
                                        <p class="text-sm text-yellow-700 font-medium">
                                            Pendaftaran Anda sedang menunggu verifikasi admin.
                                        </p>
                                        <p class="text-xs text-yellow-600">
                                            Anda masih bisa mengedit atau membatalkan pendaftaran selama status masih pending.
                                        </p>
                                    </div>
                                    
                                    {{-- Action buttons --}}
                                    <div class="flex items-center gap-3">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('pendaftaran.edit', $pendaftaran->id) }}"
                                           class="bg-yellow-600 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-yellow-700 transition">
                                            Edit Pendaftaran
                                        </a>
                                        
                                        {{-- Tombol Batalkan --}}
                                        <button type="button" 
                                                onclick="confirmCancel()"
                                                class="bg-red-600 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-red-700 transition">
                                            Batalkan
                                        </button>
                                    </div>
                                </div>

                                {{-- Modal Konfirmasi Batalkan (Hidden form) --}}
                                <form id="cancelForm" action="{{ route('pendaftaran.destroy', $pendaftaran->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                {{-- JavaScript untuk konfirmasi --}}
                                <script>
                                    function confirmCancel() {
                                        const confirmed = confirm(
                                            `Apakah Anda yakin ingin membatalkan pendaftaran magang?\n\n` +
                                            `Data yang akan dihapus:\n` +
                                            `• Nama: {{ $pendaftaran->nama_lengkap }}\n` +
                                            `• Instansi: {{ $pendaftaran->agency ?? '-' }}\n` +
                                            `• Jenis: {{ ucfirst($pendaftaran->tipe_pendaftaran) }}\n\n` +
                                            `⚠️ Peringatan: Data akan dihapus permanen dan tidak dapat dikembalikan!`
                                        );
                                        
                                        if (confirmed) {
                                            document.getElementById('cancelForm').submit();
                                        }
                                    }
                                </script>
                                @break
                                
                            @case('revisi')
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded">
                                    <p class="text-sm text-blue-700 font-medium mb-2">
                                        Beberapa data atau dokumen perlu direvisi.
                                    </p>
                                    <p class="text-xs text-blue-600 mb-3">
                                        Silakan cek catatan admin di atas dan perbaiki sesuai petunjuk.
                                    </p>
                                    <p class="text-xs text-blue-500">
                                        <strong>Catatan:</strong>  
                                        Silakan dicek kembali link Google Drive yang sudah Anda kirim,
                                        pastikan dokumen sudah lengkap dan bisa diakses oleh admin.
                                    </p>
                                </div>
                                @break
                                
                            @case('diterima')
                                <div class="p-4 bg-green-50 border border-green-200 rounded">
                                    <p class="text-sm text-green-700 font-medium">
                                        🎉 Selamat! Pendaftaran Anda sudah diterima.
                                    </p>
                                    <p class="text-xs text-green-600">
                                        Silakan menunggu informasi teknis lebih lanjut dari admin.
                                    </p>
                                </div>
                                @break
                                
                            @case('aktif')
                                <div class="p-4 bg-green-50 border border-green-200 rounded">
                                    <p class="text-sm text-green-700 font-medium">
                                        🚀 Anda sedang menjalani periode magang.
                                    </p>
                                    <p class="text-xs text-green-600">
                                        Pastikan link Google Drive tetap bisa diakses.
                                    </p>
                                </div>
                                @break
                                
                            @case('ditolak')
                                <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded">
                                    <div>
                                        <p class="text-sm text-red-700 font-medium">
                                            Pendaftaran Anda ditolak.
                                        </p>
                                        <p class="text-xs text-red-600">
                                            Silakan hubungi admin untuk informasi lebih lanjut atau ajukan pendaftaran baru.
                                        </p>
                                    </div>
                                    <a href="{{ route('pendaftaran.create') }}"
                                       class="bg-red-600 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-red-700 transition">
                                        Ajukan Ulang
                                    </a>
                                </div>
                                @break
                        @endswitch
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
