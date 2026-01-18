<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                
                // --- KODE PENGARAHAN SESUAI ROLE DIMULAI DI SINI ---
                
                // Ambil role pengguna
                $role = Auth::user()->role; 
                
                // Tentukan rute tujuan berdasarkan role
                switch ($role) {
                    case 'admin':
                        // Arahkan Admin ke /admin
                        return redirect(route('admin.dashboard')); 
                    
                    case 'driver':
                        // Arahkan Driver ke /driver
                        return redirect(route('driver.dashboard')); 
                        
                    case 'customer':
                        // Arahkan Customer ke /orders (atau /dashboard customer)
                        return redirect(route('customer.dashboard')); 
                        
                    default:
                        // Jika role tidak terdefinisi, arahkan ke dashboard umum atau home
                        return redirect('/dashboard'); 
                }

                // --- KODE PENGARAHAN SESUAI ROLE SELESAI DI SINI ---
                
            }
        }

        return $next($request);
    }
}