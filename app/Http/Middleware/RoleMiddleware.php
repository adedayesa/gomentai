<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        /**
         * LOGIKA PERBAIKAN:
         * Admin diberikan hak akses ke semua route (Super Admin).
         * Jika user bukan admin, maka dicek apakah role-nya ada dalam daftar $roles.
         */
        if ($user->role === 'admin' || in_array($user->role, $roles)) {
            return $next($request);
        }

        // 2. Jika role tidak sesuai dan bukan admin, tampilkan pesan error 403
        abort(403, 'AKSES DILARANG. ROLE ANDA ADALAH ' . strtoupper($user->role) . ' DAN ANDA TIDAK DIIZINKAN MASUK KE HALAMAN INI.');
    }
}