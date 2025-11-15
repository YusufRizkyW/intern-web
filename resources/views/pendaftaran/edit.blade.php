<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pendaftaran Magang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Info Edit --}}
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-yellow-800">Mode Edit</div>
                                <div class="text-xs text-yellow-700">
                                    Anda sedang mengedit pendaftaran yang masih berstatus <strong>pending</strong>.
                                    Setelah disimpan, admin akan mereview ulang.
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('pendaftaran.update', $pendaftaran) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Error Messages --}}
                        @if ($errors->any())
                            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="mt-1 ml-4 list-disc">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Success Message --}}
                        @if (session('success'))
                            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- 1. Pilih jenis pendaftaran --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pendaftaran</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tipe_pendaftaran" value="individu" 
                                           {{ old('tipe_pendaftaran', $pendaftaran->tipe_pendaftaran) === 'individu' ? 'checked' : '' }}
                                           id="tipe_pendaftaran_individu">
                                    <span class="text-sm">Individu</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tipe_pendaftaran" value="tim" 
                                           {{ old('tipe_pendaftaran', $pendaftaran->tipe_pendaftaran) === 'tim' ? 'checked' : '' }}
                                           id="tipe_pendaftaran_tim">
                                    <span class="text-sm">Tim / Rombongan</span>
                                </label>
                            </div>
                        </div>

                        {{-- 2. Data individu --}}
                        <div id="form_individu" class="{{ old('tipe_pendaftaran', $pendaftaran->tipe_pendaftaran) === 'individu' ? '' : 'hidden' }}">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Data Pendaftar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-700 mb-1">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" 
                                           value="{{ old('nama_lengkap', $pendaftaran->nama_lengkap) }}"
                                           class="w-full border rounded p-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-700 mb-1">NIM / NIS</label>
                                    <input type="text" name="nim" 
                                           value="{{ old('nim', $pendaftaran->nim) }}"
                                           class="w-full border rounded p-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" 
                                           value="{{ old('email', $pendaftaran->email) }}"
                                           class="w-full border rounded p-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-700 mb-1">No HP / WA</label>
                                    <input type="text" name="no_hp" 
                                           value="{{ old('no_hp', $pendaftaran->no_hp) }}"
                                           class="w-full border rounded p-2 text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-700 mb-1">Instansi / Asal</label>
                                    <input type="text" name="agency" 
                                           value="{{ old('agency', $pendaftaran->agency) }}"
                                           class="w-full border rounded p-2 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- 3. Data tim --}}
                        <div id="form_tim" class="{{ old('tipe_pendaftaran', $pendaftaran->tipe_pendaftaran) === 'tim' ? '' : 'hidden' }}">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-700">Anggota Tim</h3>
                                <button type="button" id="add_member" 
                                        class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                                    + Tambah Anggota
                                </button>
                            </div>
                            
                            <div class="md:col-span-2 mb-4">
                                <label class="block text-xs text-gray-700 mb-1">Instansi / Asal</label>
                                <input type="text" name="agency" 
                                       value="{{ old('agency', $pendaftaran->agency) }}"
                                       class="w-full border rounded p-2 text-sm">
                            </div>

                            <div id="anggota_list" class="space-y-3">
                                @php
                                    $members = old('anggota', $pendaftaran->members->toArray() ?: [['nama' => '', 'nim' => '', 'email' => '', 'no_hp' => '']]);
                                @endphp
                                @foreach($members as $index => $member)
                                    <div class="border rounded p-3 bg-white space-y-2 mb-2">
                                        <div class="flex items-center justify-between">
                                            <div class="text-xs font-semibold text-gray-600">Peserta {{ $loop->iteration }}</div>
                                            @if(!$loop->first)
                                                <button type="button" class="text-[11px] text-red-500 remove-anggota">Hapus</button>
                                            @endif
                                        </div>
                                        <input type="text" name="anggota[{{ $index }}][nama]" placeholder="Nama Lengkap"
                                               value="{{ $member['nama_anggota'] ?? $member['nama'] ?? '' }}"
                                               class="w-full border rounded p-2 text-sm" required>
                                        <input type="text" name="anggota[{{ $index }}][nim]" placeholder="NIM / NIS"
                                               value="{{ $member['nim_anggota'] ?? $member['nim'] ?? '' }}"
                                               class="w-full border rounded p-2 text-sm">
                                        <input type="email" name="anggota[{{ $index }}][email]" placeholder="Email"
                                               value="{{ $member['email_anggota'] ?? $member['email'] ?? '' }}"
                                               class="w-full border rounded p-2 text-sm">
                                        <input type="text" name="anggota[{{ $index }}][no_hp]" placeholder="No HP"
                                               value="{{ $member['no_hp_anggota'] ?? $member['no_hp'] ?? '' }}"
                                               class="w-full border rounded p-2 text-sm">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 4. Periode --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Periode Magang</label>
                            <div class="flex gap-4 mb-3">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tipe_periode" value="durasi" 
                                           {{ old('tipe_periode', $pendaftaran->tipe_periode) === 'durasi' ? 'checked' : '' }}>
                                    <span class="text-sm">Berdasarkan Durasi</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="tipe_periode" value="tanggal" 
                                           {{ old('tipe_periode', $pendaftaran->tipe_periode) === 'tanggal' ? 'checked' : '' }}>
                                    <span class="text-sm">Berdasarkan Tanggal</span>
                                </label>
                            </div>

                            {{-- Input durasi --}}
                            <div id="durasi-wrapper" class="{{ old('tipe_periode', $pendaftaran->tipe_periode) === 'durasi' ? '' : 'hidden' }}">
                                <label class="block text-xs text-gray-700 mb-1">Durasi (bulan)</label>
                                <select name="durasi_bulan" class="border rounded p-2 text-sm w-40">
                                    <option value="">-- pilih --</option>
                                    @for ($i=1; $i<=6; $i++)
                                        <option value="{{ $i }}" {{ old('durasi_bulan', $pendaftaran->durasi_bulan) == $i ? 'selected' : '' }}>{{ $i }} bulan</option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Input tanggal --}}
                            <div id="tanggal-wrapper" class="{{ old('tipe_periode', $pendaftaran->tipe_periode) === 'tanggal' ? '' : 'hidden' }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-700 mb-1">Tanggal Mulai</label>
                                        <input type="date" name="tanggal_mulai"
                                               value="{{ old('tanggal_mulai', $pendaftaran->tanggal_mulai) }}"
                                               class="border rounded p-2 text-sm w-full">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-700 mb-1">Tanggal Selesai</label>
                                        <input type="date" name="tanggal_selesai"
                                               value="{{ old('tanggal_selesai', $pendaftaran->tanggal_selesai) }}"
                                               class="border rounded p-2 text-sm w-full">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 5. Link Drive --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Link Google Drive</label>
                            <p class="text-xs text-gray-500 mb-2">
                                Masukkan link folder Google Drive yang berisi dokumen persyaratan magang Anda.
                                Pastikan folder dapat diakses oleh siapa saja dengan link.
                            </p>
                            <input type="url" name="link_drive" 
                                   value="{{ old('link_drive', $pendaftaran->link_drive) }}"
                                   placeholder="https://drive.google.com/drive/folders/..."
                                   class="w-full border rounded p-2 text-sm">
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-between">
                            <a href="{{ route('pendaftaran.status') }}"
                               class="bg-gray-500 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-gray-600">
                                Batal
                            </a>
                            <button type="submit"
                                    class="bg-red-600 text-white px-6 py-2 rounded font-semibold text-sm hover:bg-red-700">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const initialCount = {{ old('anggota') ? count(old('anggota')) : ($pendaftaran->members->count() ?: 1) }};
    </script>
    @vite('resources/js/pendaftaran.js')
</x-app-layout>