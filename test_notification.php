<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Notification System\n";
echo "===============================\n";

// 1. Cek admin user
$admin = App\Models\User::first();
if (!$admin) {
    echo "❌ Admin user tidak ditemukan\n";
    exit(1);
}
echo "✅ Admin user ditemukan: {$admin->name} ({$admin->email})\n";

// 2. Cek pendaftaran
$pendaftaran = App\Models\PendaftaranMagang::first();
if (!$pendaftaran) {
    echo "❌ Pendaftaran tidak ditemukan\n";
    exit(1);
}
echo "✅ Pendaftaran ditemukan: ID {$pendaftaran->id}\n";

// 3. Cek apakah notification class exists
if (!class_exists('App\Notifications\NewPendaftaranNotification')) {
    echo "❌ Notification class tidak ditemukan\n";
    exit(1);
}
echo "✅ Notification class ditemukan\n";

// 4. Kirim notifikasi
try {
    $admin->notify(new App\Notifications\NewPendaftaranNotification($pendaftaran));
    echo "✅ Notifikasi berhasil dikirim\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "❌ Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

// 5. Cek notifikasi tersimpan
$count = $admin->notifications()->count();
echo "📊 Total notifikasi: {$count}\n";

// 6. Cek notifikasi terbaru
$latest = $admin->notifications()->latest()->first();
if ($latest) {
    echo "📋 Notifikasi terbaru:\n";
    echo "   - ID: {$latest->id}\n";
    echo "   - Type: {$latest->type}\n";
    echo "   - Title: " . ($latest->data['title'] ?? 'N/A') . "\n";
    echo "   - Body: " . ($latest->data['body'] ?? 'N/A') . "\n";
    echo "   - Read at: " . ($latest->read_at ?? 'Unread') . "\n";
    echo "   - Created: {$latest->created_at}\n";
} else {
    echo "❌ Tidak ada notifikasi\n";
}

// 7. Cek unread notifications
$unreadCount = $admin->unreadNotifications()->count();
echo "📊 Unread notifikasi: {$unreadCount}\n";

// 8. Test Livewire component exists
if (class_exists('App\Livewire\Admin\NotificationBell')) {
    echo "✅ Livewire component ditemukan\n";
} else {
    echo "❌ Livewire component tidak ditemukan\n";
}

echo "\n✅ Test selesai\n";