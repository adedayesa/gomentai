<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    // Logika pengecekan peran: Jika user login dan perannya 'admin', izinkan
    if (auth()->check() && auth()->user()->role === 'admin') {
        return $next($request);
    }

    // Jika bukan admin, arahkan ke halaman utama atau login
    return redirect('/')->with('error', 'Anda tidak memiliki akses Admin.');
    }
}
