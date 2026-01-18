@extends('layouts.app') 

@section('title', 'Dashboard Pesanan') {{-- Ganti Judul untuk Dashboard --}}

@section('content')
<div class="container my-4">

    {{-- 1. PESAN SELAMAT DATANG (FITUR KHUSUS DASHBOARD) --}}
    <div class="alert alert-success shadow-sm mb-4 p-4 border-start border-5 border-success">
        <h4 class="alert-heading">👋 Selamat Datang Kembali, {{ auth()->user()->name }}!</h4>
        <p class="mb-0">Siap untuk memesan menu favorit Anda?</p>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    {{-- 2. INFORMASI TOKO (DARI menu.blade.php) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="card-title mb-1">Go Mentai Official Store</h5>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-star-fill text-warning me-1">⭐</i>
                        <span class="small text-muted me-3">4.7 (100+ Penilaian)</span> 
                        <span class="small text-muted"><i class="bi bi-clock"></i> Tiba dalam 30 Menit</span>
                    </div>
                </div>
                
                {{-- TOMBOL RIWAYAT PESANAN (FITUR KHUSUS DASHBOARD) --}}
                <a href="{{ route('order.history') }}" class="btn btn-outline-primary btn-sm">
                    Lihat Riwayat Pesanan
                </a>
            </div>
            
            <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="badge bg-danger">Diskon 50%</span>
                <span class="badge bg-success">Gratis Ongkir</span>
            </div>
        </div>
    </div>
    
    <h2 class="mt-4 mb-3">Pilihan Menu Kami</h2>
    
    {{-- 3. DAFTAR MENU DENGAN GAMBAR (DARI menu.blade.php) --}}
    <div class="row">
    @foreach ($products as $product) {{-- Menggunakan $products --}}
        <div class="col-6 col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <img 
                src="{{ asset($product->image ?? 'images/default.jpg') }}" 
                class="card-img-top" 
                alt="{{ $product->name }}"
                style="height: 200px; object-fit: cover;" >
        
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-1">{{ $product->name }}</h5>
                    <p class="card-text text-danger fw-bold">Rp{{ number_format($product->base_price, 0, ',', '.') }}</p>
                    
                    <div class="mt-auto text-end">
                        {{-- Menggunakan tautan detail produk --}}
                        <a href="{{ route('product.detail', $product->id) }}" class="btn btn-sm btn-success rounded-circle">+</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
</div>
@endsection