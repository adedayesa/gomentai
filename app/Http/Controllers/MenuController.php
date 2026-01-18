<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index() 
    {
        $products = Product::with('category', 'options.values')->get(); 

        if (Auth::check() && Auth::user()->role === 'customer') { 
            return view('customer.dashboard', compact('products')); 
        } elseif (Auth::check() && Auth::user()->role === 'admin') {
            return view('admin.menus.index', compact('products'));
        }
        
        return view('menu', compact('products')); 
    }

    // 1. Tampilan Form Tambah Menu
    public function create()
    {
        return view('admin.menus.create');
    }

    // 2. Proses Simpan Menu Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'base_price' => 'required|numeric', // Diubah ke base_price agar sinkron database
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('image')->store('products', 'public');

        Product::create([
            'name' => $request->name,
            'base_price' => $request->base_price, // Diubah ke base_price
            'description' => $request->description,
            'image' => 'storage/' . $path, // Menambahkan prefix storage/ agar gambar muncul
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil dibuat!');
    }

    // 3. Tampilan Form Edit
    public function edit($id)
    {
        // Menggunakan findOrFail untuk memastikan data ditemukan sebelum dilempar ke view
        $menu = Product::findOrFail($id);
        return view('admin.menus.edit', compact('menu'));
    }

    // 4. Proses Update Menu
    public function update(Request $request, $id)
    {
        $menu = Product::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'base_price' => 'required|numeric', // Diubah ke base_price
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama (menghapus prefix 'storage/' jika ada sebelum delete)
            $oldPath = str_replace('storage/', '', $menu->image);
            Storage::disk('public')->delete($oldPath);
            
            $path = $request->file('image')->store('products', 'public');
            $menu->image = 'storage/' . $path;
        }

        $menu->update([
            'name' => $request->name,
            'base_price' => $request->base_price, // Diubah ke base_price
            'description' => $request->description
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu diperbarui!');
    }

    // 5. Proses Hapus Menu
    public function destroy($id)
    {
        $menu = Product::findOrFail($id);

        if ($menu->image) {
            $path = str_replace('storage/', '', $menu->image);
            Storage::disk('public')->delete($path);
        }
        
        $menu->delete();
        return redirect()->back()->with('success', 'Menu berhasil dihapus!');
    }

    public function detail(Product $product)
    {
        $product->load(['options.values']); 
        return view('product_detail', compact('product'));
    }
}