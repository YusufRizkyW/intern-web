<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Magang Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($riwayat->isEmpty())
                        <p class="text-sm text-gray-500">
                            Belum ada riwayat magang. Data akan muncul di sini setelah periode magang kamu selesai dan diarsipkan admin.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100 text-left text-xs text-gray-500 uppercase">
                                    <tr>
                                        <th class="px-4 py-2">Instansi</th>
                                        <th class="px-4 py-2">Periode</th>
                                        {{-- <th class="px-4 py-2">Posisi</th> --}}
                                        <th class="px-4 py-2">Catatan Admin</th>
                                        <th class="px-4 py-2">Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($riwayat as $item)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-800">
                                                    {{ $item->instansi ?? 'BPS Gresik' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $item->nama_lengkap }}
                                                    @if ($item->nim)
                                                        ({{ $item->nim }})
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                @if ($item->tanggal_mulai && $item->tanggal_selesai)
                                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                                    –
                                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                            {{-- <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $item->posisi ?? '-' }}
                                            </td> --}}
                                            <td class="px-4 py-3 text-xs text-gray-700">
                                                {{ $item->catatan_admin ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-700">
                                                @if ($item->file_sertifikat)
                                                    <a href="{{ asset('storage/' . $item->file_sertifikat) }}"
                                                       class="inline-flex items-center gap-1 bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-700"
                                                       target="_blank">
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
