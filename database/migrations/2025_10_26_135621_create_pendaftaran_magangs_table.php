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
        Schema::create('pendaftaran_magang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // kalau pendaftaran harus login
            $table->string('nama_lengkap');
            $table->string('agency')->required();
            $table->string('nim')->nullable();
            $table->string('email');
            $table->string('no_hp')->nullable();
            // status proses pendaftaran
            $table->enum('status_verifikasi', ['pending', 'revisi', 'diterima', 'ditolak', 'aktif', 'selesai', 'arsip', 'batal'])
                ->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
