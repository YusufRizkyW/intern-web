<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuota_magangs', function (Blueprint $table) {
            $table->id();
            $table->year('tahun'); // 2024
            $table->tinyInteger('bulan'); // 1-12
            $table->integer('kuota_maksimal')->default(0); // Max peserta per bulan
            $table->integer('kuota_terisi')->default(0); // Jumlah peserta saat ini
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->text('catatan')->nullable(); // Catatan admin
            $table->timestamps();

            // Index untuk performa
            $table->unique(['tahun', 'bulan'], 'unique_tahun_bulan');
            $table->index(['tahun', 'bulan', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuota_magangs');
    }
};