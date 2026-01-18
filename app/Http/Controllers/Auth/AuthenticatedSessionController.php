<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); 
        $request->session()->regenerate();
        
        // Ambil role pengguna
        $role = Auth::user()->role;
        
        // Gunakan switch untuk pengarahan berbasis role
        switch ($role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard')); 
            case 'driver':
                return redirect()->intended(route('driver.dashboard')); 
            case 'customer':
                return redirect()->intended(route('customer.dashboard')); 
            default:
                // Fallback aman jika role tidak dikenali
                return redirect()->intended(RouteServiceProvider::HOME); 
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
