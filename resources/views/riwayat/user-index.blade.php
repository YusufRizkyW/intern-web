<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 text-gray-900 space-y-6">

                    {{-- Flash success --}}
                    @if (session('success'))
                        <div class="p-3 bg-green-100 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Jika belum ada riwayat --}}
                    @if ($riwayat->isEmpty())
                        <div class="text-center py-12 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                 fill="none" 
                                 viewBox="0 0 24 24" 
                                 stroke-width="1.5" 
                                 stroke="currentColor"
                                 class="mx-auto w-12 h-12 text-gray-400 mb-3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6v6l4 2m6 4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-medium">Belum ada riwayat magang.</p>
                            <p class="text-xs text-gray-500">
                                Riwayat pendaftaran akan muncul di sini setelah periode magang kamu selesai atau dibatalkan.
                            </p>
                        </div>
                    @else
                        {{-- Tabel Riwayat --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm border border-gray-100">
                                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                                    <tr>
                                        <th class="px-4 py-2 border-b">Instansi / Asal</th>
                                        <th class="px-4 py-2 border-b">Periode</th>
                                        <th class="px-4 py-2 border-b">Status</th>
                                        <th class="px-4 py-2 border-b">Peserta / Tim</th>
                                        <th class="px-4 py-2 border-b">Catatan Admin</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($riwayat as $item)
                                        @php
                                            $pendaftaran = $item->pendaftaranMagang ?? null;
                                            $isTim = $pendaftaran?->tipe_pendaftaran === 'tim';
                                            $anggota = $pendaftaran?->members ?? collect();
                                            
                                            // Status styling
                                            $statusClass = match($item->status_verifikasi) {
                                                'selesai' => 'bg-green-100 text-green-800',
                                                'batal' => 'bg-red-100 text-red-800',
                                                'ditolak' => 'bg-red-100 text-red-800',
                                                'arsip' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-blue-100 text-blue-800'
                                            };
                                            
                                            $statusText = match($item->status_verifikasi) {
                                                'selesai' => 'Selesai',
                                                'batal' => 'Dibatalkan',
                                                'ditolak' => 'Ditolak',
                                                'arsip' => 'Diarsipkan',
                                                default => ucfirst($item->status_verifikasi)
                                            };
                                        @endphp

                                        <tr class="hover:bg-gray-50 transition-colors">
                                            {{-- Instansi --}}
                                            <td class="px-4 py-3 align-top">
                                                <div class="font-semibold text-gray-800">
                                                    {{ $pendaftaran?->agency ?? $item->instansi_asal ?? '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $pendaftaran?->tipe_pendaftaran === 'tim' ? 'Tim / Rombongan' : 'Individu' }}
                                                </div>
                                            </td>

                                            {{-- Periode --}}
                                            <td class="px-4 py-3 align-top text-sm text-gray-700 whitespace-nowrap">
                                                @if ($item->tanggal_mulai && $item->tanggal_selesai)
                                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                                                    &nbsp;–&nbsp;
                                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y') }}
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>

                                            {{-- Status --}}
                                            <td class="px-4 py-3 align-top">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                    @if($item->status_verifikasi === 'selesai')
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @elseif(in_array($item->status_verifikasi, ['batal', 'ditolak']))
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @elseif($item->status_verifikasi === 'arsip')
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                                            <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                    {{ $statusText }}
                                                </span>
                                            </td>

                                            {{-- Peserta --}}
                                            <td class="px-4 py-3 align-top text-xs text-gray-700">
                                                @if ($isTim)
                                                    <div class="font-medium text-gray-800 text-sm">
                                                        Ketua: {{ $pendaftaran->nama_lengkap }}
                                                    </div>
                                                    <ul class="list-disc list-inside text-[11px] text-gray-600 mt-1">
                                                        @foreach ($anggota as $m)
                                                            <li>
                                                                {{ $m->nama_anggota }}
                                                                @if ($m->nim_anggota)
                                                                    ({{ $m->nim_anggota }})
                                                                @endif
                                                                @if ($m->is_ketua)
                                                                    <span class="text-blue-600 font-semibold">(Ketua)</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <div class="text-sm text-gray-800 font-medium">
                                                        {{ $pendaftaran?->nama_lengkap ?? $item->nama_lengkap }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $pendaftaran?->nim ?? $item->nim ?? '-' }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Catatan Admin --}}
                                            <td class="px-4 py-3 align-top text-xs text-gray-700 max-w-xs break-words">
                                                @if($item->catatan_admin)
                                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-2 rounded">
                                                        <div class="text-xs text-yellow-800">
                                                            {{ $item->catatan_admin }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
