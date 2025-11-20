<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Auto-Notification\n";
echo "=============================\n";

// 1. Cek admin user
$admin = App\Models\User::first();
$beforeCount = $admin->notifications()->count();
echo "📊 Notifikasi sebelum: {$beforeCount}\n";

// 2. Buat pendaftaran baru untuk trigger observer
$pendaftaran = App\Models\PendaftaranMagang::create([
    'user_id' => $admin->id, // Gunakan admin sebagai user untuk test
    'nama_lengkap' => 'Test User Auto Notification',
    'agency' => 'Test University',
    'nim' => '123456789',
    'email' => 'test@example.com',
    'no_hp' => '081234567890',
    'jumlah_peserta' => 1,
    'tanggal_mulai' => '2025-01-01',
    'tanggal_selesai' => '2025-01-31',
    'link_drive' => 'https://drive.google.com/test',
    'status_verifikasi' => 'pending',
    'tipe_pendaftaran' => 'individu',
]);

echo "✅ Pendaftaran baru dibuat: ID {$pendaftaran->id}\n";

// 3. Tunggu sebentar untuk memastikan observer jalan
sleep(1);

// 4. Cek notifikasi setelah
$afterCount = $admin->notifications()->count();
echo "📊 Notifikasi setelah: {$afterCount}\n";

if ($afterCount > $beforeCount) {
    echo "✅ Auto-notification berhasil! (+". ($afterCount - $beforeCount) . " notifikasi)\n";
    
    // Cek notifikasi terbaru
    $latest = $admin->notifications()->latest()->first();
    echo "📋 Notifikasi terbaru:\n";
    echo "   - Type: {$latest->type}\n";
    echo "   - Title: " . ($latest->data['title'] ?? 'N/A') . "\n";
    echo "   - Body: " . ($latest->data['body'] ?? 'N/A') . "\n";
    echo "   - Created: {$latest->created_at}\n";
} else {
    echo "❌ Auto-notification tidak bekerja\n";
}

echo "\n✅ Test auto-notification selesai\n";