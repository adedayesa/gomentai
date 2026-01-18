<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Penambahan baris ini untuk memperbaiki error

class UserController extends Controller
{
    // Index: Menampilkan daftar pengguna (route: users.index)
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    // Show, Edit, Update, Destroy (akan ditambahkan logikanya nanti)
    
    // Method untuk menampilkan form edit
    public function edit(User $user)
    {
        // Daftar role yang diizinkan untuk dipilih oleh Admin
        $roles = ['customer', 'driver', 'admin']; 
        
        // Pastikan Admin tidak mengedit role dirinya sendiri (opsional)
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                             ->with('error', 'Anda tidak bisa mengubah peran Anda sendiri melalui halaman ini.');
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    // Method untuk menyimpan perubahan role (Inti dari fitur kita!)
    public function update(Request $request, User $user)
    {
        // 1. Validasi input role
        $validated = $request->validate([
            'role' => 'required|in:customer,driver,admin',
        ]);
        
        // 2. Simpan peran baru
        $user->role = $validated['role'];
        $user->save();
        
        return redirect()->route('users.index')->with('success', "Role pengguna {$user->name} berhasil diperbarui menjadi {$user->role}.");
    }

    // Method lainnya harus ada untuk resource controller, meskipun kosong
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function show(User $user) { abort(404); }
    public function destroy(User $user) { /* ... */ } 
}