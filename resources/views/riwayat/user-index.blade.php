<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Magang Saya') }}
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
                                Data akan muncul di sini setelah periode magang kamu selesai dan diarsipkan oleh admin.
                            </p>
                        </div>
                    @else
                        {{-- Tabel Riwayat --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm border border-gray-100">
                                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                                    <tr>
                                        <th class="px-4 py-2 border-b">Instansi</th>
                                        <th class="px-4 py-2 border-b">Periode</th>
                                        <th class="px-4 py-2 border-b">Catatan Admin</th>
                                        <th class="px-4 py-2 border-b text-center">Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($riwayat as $item)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            {{-- Instansi --}}
                                            <td class="px-4 py-3 align-top">
                                                <div class="font-semibold text-gray-800">
                                                    {{ $item->instansi_asal ?? 'BPS Gresik' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $item->nama_lengkap }}
                                                    @if ($item->nim)
                                                        ({{ $item->nim }})
                                                    @endif
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

                                            {{-- Catatan Admin --}}
                                            <td class="px-4 py-3 align-top text-xs text-gray-700 max-w-xs break-words">
                                                {{ $item->catatan_admin ?? '-' }}
                                            </td>

                                            {{-- Sertifikat --}}
                                            <td class="px-4 py-3 align-top text-center">
                                                @if ($item->file_sertifikat)
                                                    <a href="{{ asset('storage/' . $item->file_sertifikat) }}"
                                                       target="_blank"
                                                       class="inline-flex items-center gap-1 bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-700 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                                             fill="none" 
                                                             viewBox="0 0 24 24" 
                                                             stroke-width="1.5" 
                                                             stroke="currentColor"
                                                             class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                        Unduh
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 text-xs">Belum ada</span>
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
