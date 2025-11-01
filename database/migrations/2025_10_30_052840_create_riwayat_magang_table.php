<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_magangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_magang_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_lengkap')->required();
            $table->string('agency')->required(); 
            $table->string('nim')->required();
            $table->string('email')->required();
            $table->string('no_hp')->required();
            $table->string('link_drive')->nullable();
            $table->string('catatan_admin')->nullable();
            $table->enum('status_verifikasi', ['pending', 'revisi', 'diterima', 'ditolak', 'aktif', 'selesai', 'arsip', 'batal'])
                ->default('selesai');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('file_sertifikat')->nullable(); // path ke storage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_magang');
    }
};
