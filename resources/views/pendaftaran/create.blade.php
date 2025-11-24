<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pendaftaran Magang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- info akun yg login --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-sm text-gray-700">
                    Login sebagai:
                    <span class="font-semibold">{{ auth()->user()->name }}</span>
                    <span class="text-gray-500">({{ auth()->user()->email }})</span>
                    <p class="text-xs text-gray-400 mt-1">
                        Catatan: Anda boleh mendaftarkan diri sendiri atau orang lain dari akun ini.
                    </p>
                </div>
            </div>

            {{-- form --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- error --}}
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                            <ul class="text-sm list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- success --}}
                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('pendaftaran.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- 1. Jenis pendaftaran --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Jenis Pendaftaran
                            </label>
                            <select name="tipe_pendaftaran" id="tipe_pendaftaran"
                                    class="w-full border rounded p-2 text-sm focus:ring-red-500 focus:border-red-500">
                                <option value="">-- pilih --</option>
                                <option value="individu" {{ old('tipe_pendaftaran','individu') === 'individu' ? 'selected' : '' }}>Individu (1 orang)</option>
                                <option value="tim" {{ old('tipe_pendaftaran') === 'tim' ? 'selected' : '' }}>Tim</option>
                            </select>
                            <p class="text-xs text-gray-500">
                                Pilih <b>Tim</b> kalau Anda mewakili sekolah dan ingin mendaftarkan beberapa siswa sekaligus.
                            </p>
                        </div>

                        {{-- NEW: agency tunggal (dipindah di sini) --}}
                        <div class="mt-2">
                            <label class="block text-sm text-gray-700">Asal / Instansi / Kampus <span class="text-red-500">*</span></label>
                            <input type="text" name="agency"
                                value="{{ old('agency') }}"
                                class="w-full border rounded p-2 text-sm"
                                placeholder="Contoh: Universitas X / SMK Y"
                                required>
                        </div>


                        {{-- 2A. Data peserta (INDIVIDU) --}}
                        <div id="form_individu" class="{{ old('tipe_pendaftaran','individu') === 'individu' ? '' : 'hidden' }} space-y-4 border rounded p-4 mt-2">
                            <h3 class="text-sm font-semibold text-gray-700">Data Peserta</h3>

                            <div>
                                <label class="block text-sm text-gray-700">Nama Lengkap Peserta <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_lengkap"
                                    value="{{ old('nama_lengkap') }}"
                                    class="w-full border rounded p-2 text-sm">
                            </div>
                            {{-- <div>
                                <label class="block text-sm text-gray-700">Asal / Instansi / Kampus <span class="text-red-500">*</span></label>
                                <input type="text" name="agency"
                                    value="{{ old('agency') }}"
                                    class="w-full border rounded p-2 text-sm">
                            </div> --}}
                            <div>
                                <label class="block text-sm text-gray-700">NIM / NIS <span class="text-red-500">*</span></label>
                                <input type="text" name="nim"
                                    value="{{ old('nim') }}"
                                    class="w-full border rounded p-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email"
                                    value="{{ old('email') }}"
                                    class="w-full border rounded p-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">No HP <span class="text-red-500">*</span></label>
                                <input type="text" name="no_hp"
                                    value="{{ old('no_hp') }}"
                                    class="w-full border rounded p-2 text-sm">
                            </div>
                            
                        </div>

                        {{-- 2B. Data peserta (TIM) --}}
                        <div id="form_tim" class="{{ old('tipe_pendaftaran') === 'tim' ? '' : 'hidden' }} space-y-4 border rounded p-4 bg-gray-50 mt-2">
                            <h3 class="text-sm font-semibold text-gray-700">Daftar Peserta (Tim)</h3>

                            {{-- agency untuk seluruh tim --}}
                            {{-- <div>
                                <label class="block text-sm text-gray-700">Asal / Instansi / Kampus <span class="text-red-500">*</span></label>
                                <input type="text" name="agency"
                                    value="{{ old('agency') }}"
                                    class="w-full border rounded p-2 text-sm">
                            </div> --}}

                            <p class="text-xs text-gray-500 mb-2">
                                Peserta 1 akan dianggap ketua / kontak utama.
                            </p>

                            <div id="anggota_list">
                                @if (old('anggota'))
                                    @foreach (old('anggota') as $i => $row)
                                        <div class="border rounded p-3 bg-white space-y-2 mb-2">
                                            <div class="flex items-center justify-between">
                                                <div class="text-xs font-semibold text-gray-600">
                                                    Peserta {{ $i+1 }} {{ $i===0 ? '(Ketua)' : '' }}
                                                </div>
                                                @if ($i !== 0)
                                                    <button type="button" class="text-[11px] text-red-500 remove-anggota">Hapus</button>
                                                @endif
                                            </div>
                                            <input type="text" name="anggota[{{ $i }}][nama]" placeholder="Nama Lengkap"
                                                   value="{{ $row['nama'] ?? '' }}"
                                                   class="w-full border rounded p-2 text-sm" {{ $i===0 ? 'required' : '' }}>
                                            <input type="text" name="anggota[{{ $i }}][nim]" placeholder="NIM / NIS"
                                                   value="{{ $row['nim'] ?? '' }}"
                                                   class="w-full border rounded p-2 text-sm">
                                            {{-- <input type="text" name="anggota[{{ $i }}][agency]" placeholder="Asal / Instansi / Kampus"
                                                   value="{{ $row['agency'] ?? '' }}"
                                                   class="w-full border rounded p-2 text-sm"> --}}
                                            <input type="email" name="anggota[{{ $i }}][email]" placeholder="Email"
                                                   value="{{ $row['email'] ?? '' }}"
                                                   class="w-full border rounded p-2 text-sm">
                                            <input type="text" name="anggota[{{ $i }}][no_hp]" placeholder="No HP"
                                                   value="{{ $row['no_hp'] ?? '' }}"
                                                   class="w-full border rounded p-2 text-sm">
                                        </div>
                                    @endforeach
                                @else
                                    {{-- default 1 peserta --}}
                                    <div class="border rounded p-3 bg-white space-y-2 mb-2">
                                        <div class="text-xs font-semibold text-gray-600">Peserta 1 (Ketua)</div>
                                        <input type="text" name="anggota[0][nama]" placeholder="Nama Lengkap"
                                               class="w-full border rounded p-2 text-sm">
                                        {{-- <input type="text" name="anggota[0][agency]" placeholder="Asal / Instansi / Kampus"
                                               class="w-full border rounded p-2 text-sm"> --}}
                                        <input type="text" name="anggota[0][nim]" placeholder="NIM / NIS"
                                               class="w-full border rounded p-2 text-sm">
                                        <input type="email" name="anggota[0][email]" placeholder="Email"
                                               class="w-full border rounded p-2 text-sm">
                                        <input type="text" name="anggota[0][no_hp]" placeholder="No HP"
                                               class="w-full border rounded p-2 text-sm">
                                    </div>
                                @endif
                            </div>

                            <button type="button" id="add_member" class="text-red-600 text-xs underline">
                                + Tambah Peserta
                            </button>
                        </div>

                        <hr class="my-6">

                        {{-- 3. Periode --}}
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700">Periode Magang</label>

                            <div class="space-y-4 border rounded p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal_mulai"
                                               value="{{ old('tanggal_mulai') }}"
                                               class="border rounded p-2 text-sm w-full" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal_selesai"
                                               value="{{ old('tanggal_selesai') }}"
                                               class="border rounded p-2 text-sm w-full" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-6">

                        {{-- 4. Link drive --}}
                        <div>
                            <label class="block text-sm font-medium">Link Folder Google Drive</label>
                            <input type="url" name="link_drive" value="{{ old('link_drive') }}"
                                   class="w-full border rounded p-2 text-sm" required>
                            <p class="text-xs text-gray-500 mt-1">
                                Silahkan upload dokumen (surat pengantar) dan salin link folder Google Drive yang berisi dokumen peserta, pastikan pengaturan link dapat diakses oleh siapa saja yang memiliki link.
                            </p>
                        </div>

                        {{-- submit --}}
                        <div>
                            <button type="submit"
                                    class="w-full bg-red-600 text-white p-2 rounded text-sm font-semibold hover:bg-red-700">
                                Kirim Pendaftaran
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
    
    <script>
        const initialCount = {{ old('anggota') ? count(old('anggota')) : 1 }};
    </script>
    @vite('resources/js/pendaftaran.js')
</x-app-layout>
