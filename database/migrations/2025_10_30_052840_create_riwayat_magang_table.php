<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('agency')->nullable(); 
            $table->string('nim')->nullable();
            $table->string('email');
            $table->string('no_hp')->nullable();
            $table->enum('status_verifikasi', ['pending', 'revisi', 'diterima', 'ditolak', 'aktif', 'selesai', 'arsip', 'batal'])
                ->default('selesai');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->string('file_sertifikat')->nullable(); // path ke storage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_magang');
    }
};
