<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_magang_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_magang_id')
                ->constrained('pendaftaran_magangs')
                ->onDelete('cascade');

            $table->string('nama_anggota');
            $table->string('agency_anggota')->nullable();
            $table->string('nim_anggota')->nullable();
            $table->string('email_anggota')->nullable();
            $table->string('no_hp_anggota')->nullable();
            $table->boolean('is_ketua')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_magang_members');
    }
};
