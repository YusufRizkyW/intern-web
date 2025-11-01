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
        Schema::table('pendaftaran_magangs', function (Blueprint $table) {
            $table->enum('tipe_periode', ['durasi', 'tanggal'])->default('durasi');
            $table->integer('durasi_bulan')->nullable(); // contoh: 1, 2, 3
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_magang', function (Blueprint $table) {
            $table->dropColumn(['tipe_periode', 'durasi_bulan', 'tanggal_mulai', 'tanggal_selesai']);
        });
    }

};
