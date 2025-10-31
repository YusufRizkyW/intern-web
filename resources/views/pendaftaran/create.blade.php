<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pendaftaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Card info atas --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-sm">
                    {{ __("You're on pendaftaran page!") }}
                    <br>
                    <span class="text-gray-500">
                        Silakan isi data diri dan unggah berkas persyaratan magang.
                    </span>
                </div>
            </div>

            {{-- Card form pendaftaran --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h1 class="text-lg font-semibold mb-4">Pendaftaran Magang</h1>

                    {{-- tampilkan error validasi --}}
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                            <ul class="text-sm list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- tampilkan flash success --}}
                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- ====== Data diri ====== --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    name="nama_lengkap"
                                    class="w-full border rounded p-2 text-sm focus:ring-red-500 focus:border-red-500"
                                    value="{{ old('nama_lengkap', auth()->user()->name ?? '') }}"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIM</label>
                                <input
                                    type="text"
                                    name="nim"
                                    class="w-full border rounded p-2 text-sm focus:ring-red-500 focus:border-red-500"
                                    value="{{ old('nim') }}"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                                <input
                                    type="email"
                                    name="email"
                                    class="w-full border rounded p-2 text-sm focus:ring-red-500 focus:border-red-500"
                                    value="{{ old('email', auth()->user()->email ?? '') }}"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. HP / WA</label>
                                <input
                                    type="text"
                                    name="no_hp"
                                    class="w-full border rounded p-2 text-sm focus:ring-red-500 focus:border-red-500"
                                    value="{{ old('no_hp') }}"
                                >
                            </div>
                        </div>

                        <hr class="my-6">

                        {{-- ====== Periode Magang ====== --}}
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700">Periode Magang</label>

                            <div class="space-y-6 border rounded p-4">

                                {{-- Opsi durasi bulan --}}
                                <div class="space-y-3">
                                    <label class="flex items-start gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="tipe_periode"
                                            value="durasi"
                                            class="mt-1 text-red-600 border-gray-300 focus:ring-red-500"
                                            {{ old('tipe_periode', 'durasi') === 'durasi' ? 'checked' : '' }}
                                        >
                                        <span>
                                            <span class="font-medium text-sm text-gray-800">Pilih durasi magang</span>
                                            <span class="block text-xs text-gray-500">Contoh: 1 bulan, 2 bulan, 3 bulan...</span>
                                        </span>
                                    </label>

                                    <div class="ml-6">
                                        <label class="block text-xs text-gray-700 mb-1">Durasi (bulan)</label>
                                        <select
                                            name="durasi_bulan"
                                            class="border rounded p-2 w-40 text-sm focus:ring-red-500 focus:border-red-500"
                                        >
                                            <option value="">-- pilih --</option>
                                            <option value="1" {{ old('durasi_bulan') == 1 ? 'selected' : '' }}>1 bulan</option>
                                            <option value="2" {{ old('durasi_bulan') == 2 ? 'selected' : '' }}>2 bulan</option>
                                            <option value="3" {{ old('durasi_bulan') == 3 ? 'selected' : '' }}>3 bulan</option>
                                            <option value="4" {{ old('durasi_bulan') == 4 ? 'selected' : '' }}>4 bulan</option>
                                            <option value="5" {{ old('durasi_bulan') == 5 ? 'selected' : '' }}>5 bulan</option>
                                            <option value="6" {{ old('durasi_bulan') == 6 ? 'selected' : '' }}>6 bulan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="border-t pt-4"></div>

                                {{-- Opsi tanggal custom --}}
                                <div class="space-y-3">
                                    <label class="flex items-start gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="tipe_periode"
                                            value="tanggal"
                                            class="mt-1 text-red-600 border-gray-300 focus:ring-red-500"
                                            {{ old('tipe_periode') === 'tanggal' ? 'checked' : '' }}
                                        >
                                        <span>
                                            <span class="font-medium text-sm text-gray-800">Pilih tanggal mulai & selesai</span>
                                            <span class="block text-xs text-gray-500">Contoh: 2025-07-01 sampai 2025-09-30</span>
                                        </span>
                                    </label>

                                    <div class="ml-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs text-gray-700 mb-1">Tanggal Mulai</label>
                                            <input
                                                type="date"
                                                name="tanggal_mulai"
                                                class="border rounded p-2 w-full text-sm focus:ring-red-500 focus:border-red-500"
                                                value="{{ old('tanggal_mulai') }}"
                                            >
                                        </div>

                                        <div>
                                            <label class="block text-xs text-gray-700 mb-1">Tanggal Selesai</label>
                                            <input
                                                type="date"
                                                name="tanggal_selesai"
                                                class="border rounded p-2 w-full text-sm focus:ring-red-500 focus:border-red-500"
                                                value="{{ old('tanggal_selesai') }}"
                                            >
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <hr class="my-6">

                        {{-- ====== Upload berkas ====== --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">CV (PDF) <span class="text-red-500">*</span></label>
                                <input
                                    type="file"
                                    name="cv"
                                    accept="application/pdf"
                                    class="w-full border rounded p-2 text-sm focus:ring-red-500 focus:border-red-500"
                                    required
                                >
                                <p class="text-xs text-gray-500 mt-1">Format: PDF, max 2MB</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Surat Pengantar Kampus (PDF) <span class="text-red-500">*</span></label>
                                <input
                                    type="file"
                                    name="surat_pengantar"
                                    accept="application/pdf"
                                    class="w-full border rounded p-2 text-sm focus:ring-red-500 focus:border-red-500"
                                    required
                                >
                                <p class="text-xs text-gray-500 mt-1">Ditandatangani dan distempel kampus</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kartu Mahasiswa / KTM (PDF) <span class="text-red-500">*</span></label>
                                <input
                                    type="file"
                                    name="ktm"
                                    accept="application/pdf"
                                    class="w-full border rounded p-2 text-sm focus:ring-red-500 focus:border-red-500"
                                    required
                                >
                            </div>
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="w-full bg-red-600 text-white p-2 rounded font-semibold text-sm hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Kirim Pendaftaran
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
