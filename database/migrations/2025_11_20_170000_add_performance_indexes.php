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
        // Hanya tambah index untuk tabel yang pasti ada dan kolom yang diperlukan untuk performa

        // Index untuk tabel notifications (untuk notification bell)
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_id', 'read_at'], 'idx_notifiable_read_perf');
                $table->index('created_at', 'idx_notifications_created_perf');
            });
        }

        // Index untuk tabel kuota_magangs (untuk dashboard kuota)
        if (Schema::hasTable('kuota_magangs')) {
            Schema::table('kuota_magangs', function (Blueprint $table) {
                $table->index(['tahun', 'bulan', 'is_active'], 'idx_kuota_periode_active');
                $table->index(['is_active', 'kuota_terisi'], 'idx_kuota_active_terisi');
            });
        }

        // Index untuk tabel riwayat_magang (untuk riwayat user)
        if (Schema::hasTable('riwayat_magang')) {
            Schema::table('riwayat_magang', function (Blueprint $table) {
                $table->index(['user_id', 'status_verifikasi'], 'idx_riwayat_user_status');
                $table->index('created_at', 'idx_riwayat_created');
            });
        }

        // Index untuk tabel pendaftaran_status_logs (untuk tracking perubahan)
        if (Schema::hasTable('pendaftaran_status_logs')) {
            Schema::table('pendaftaran_status_logs', function (Blueprint $table) {
                $table->index(['pendaftaran_magang_id', 'created_at'], 'idx_status_log_pendaftaran');
                $table->index('admin_user_id', 'idx_status_log_admin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('idx_notifiable_read_perf');
                $table->dropIndex('idx_notifications_created_perf');
            });
        }

        if (Schema::hasTable('kuota_magangs')) {
            Schema::table('kuota_magangs', function (Blueprint $table) {
                $table->dropIndex('idx_kuota_periode_active');
                $table->dropIndex('idx_kuota_active_terisi');
            });
        }

        if (Schema::hasTable('riwayat_magang')) {
            Schema::table('riwayat_magang', function (Blueprint $table) {
                $table->dropIndex('idx_riwayat_user_status');
                $table->dropIndex('idx_riwayat_created');
            });
        }

        if (Schema::hasTable('pendaftaran_status_logs')) {
            Schema::table('pendaftaran_status_logs', function (Blueprint $table) {
                $table->dropIndex('idx_status_log_pendaftaran');
                $table->dropIndex('idx_status_log_admin');
            });
        }
    }
};