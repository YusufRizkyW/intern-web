<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftaran_magangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // kalau pendaftaran harus login
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('nama_lengkap')->required();
            $table->string('agency')->required();
            $table->enum('tipe_pendaftaran', ['individu', 'tim'])
                ->default('individu');
            $table->unsignedInteger('jumlah_anggota')
                ->nullable();
            $table->string('nim')->required();
            $table->string('email')->required();
            $table->string('no_hp')->required();
            $table->string('link_drive')->required();
            $table->string('catatan_admin')->nullable();
            // status proses pendaftaran
            $table->enum('status_verifikasi', ['pending', 'revisi', 'diterima', 'ditolak', 'aktif', 'selesai', 'arsip', 'batal'])
                ->default('pending');
            $table->timestamps();

            
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_magangs');
    }
};
