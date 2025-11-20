<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PendaftaranMagangController;
use App\Http\Controllers\PendaftaranMagangStatusController;
use App\Http\Controllers\RiwayatMagangUserController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/dashboard');
});

// dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');


// ====================
// AREA USER TERLOGIN
// ====================
Route::middleware(['auth'])->group(function () {
    // Form pendaftaran magang (GET & POST)
    Route::get('/pendaftaran-magang', [PendaftaranMagangController::class, 'create'])
        ->name('pendaftaran.create');

    Route::post('/pendaftaran-magang', [PendaftaranMagangController::class, 'store'])
        ->name('pendaftaran.store');

    // Halaman status pendaftaran user
    Route::get('/status-pendaftaran', [PendaftaranMagangStatusController::class, 'show'])
        ->name('pendaftaran.status');

    // Kalau kamu mau punya link menu "Pendaftaran" di navbar,
    // kamu bisa tambahkan alias ini biar route('pendaftaran') juga jalan:
    Route::get('/pendaftaran', [PendaftaranMagangController::class, 'create'])
        ->name('pendaftaran');

    // Halaman riwayat magang user
    Route::get('/riwayat-magang', [RiwayatMagangUserController::class, 'index'])
        ->name('riwayat.user.index');

    
    
    // Edit dan update pendaftaran (hanya untuk status pending)
    Route::get('/pendaftaran/{pendaftaran}/edit', [PendaftaranMagangController::class, 'edit'])
        ->name('pendaftaran.edit');
    Route::put('/pendaftaran/{pendaftaran}', [PendaftaranMagangController::class, 'update'])
        ->name('pendaftaran.update');
    
    // Batalkan pendaftaran (hanya untuk status pending)
    Route::delete('/pendaftaran/{pendaftaran}', [PendaftaranMagangController::class, 'destroy'])
        ->name('pendaftaran.destroy');

});


// ====================
// PROFILE ROUTES
// ====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route::get('/whoami', function () {
//     return auth()->user();
// });


require __DIR__.'/auth.php';
