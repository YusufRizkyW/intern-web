<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil user yang login
        $user = Auth::user();

        // Kalau belum login -> IZINKAN LEWAT
        // (Biar Filament sendiri yang redirect ke /admin/login kalau perlu)
        if (! $user) {
            return $next($request);
        }

        // Kalau SUDAH login tapi role-nya bukan admin -> blokir
        if ($user->role !== 'admin') {
            abort(403, 'Anda tidak punya akses ke panel admin.');
        }

        // Kalau SUDAH login dan role admin -> lolos
        return $next($request);
    }
}
