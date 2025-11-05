@php
    /** @var \App\Models\RiwayatMagang|null $record */
    $pendaftaran = $record?->pendaftaran;
    $members = $pendaftaran?->members;
@endphp

@if (! $pendaftaran || ! $members || $members->isEmpty())
    <p class="text-xs text-gray-400">
        Tidak ada data anggota tim. Pendaftaran ini kemungkinan individu.
    </p>
@else
    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
        @foreach ($members as $member)
            <li>
                {{ $member->nama_anggota }}
                @if ($member->nim_anggota)
                    ({{ $member->nim_anggota }})
                @endif

                @if ($member->is_ketua ?? false)
                    <span class="text-[11px] text-green-600 font-semibold">– Ketua</span>
                @endif
            </li>
        @endforeach
    </ul>
@endif
