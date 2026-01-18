@extends('layouts.app') 

@section('title', 'Menu Go Mentai')

@section('content')
    
    <div class="card bg-light border-0 mb-4">
        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
            
            @if(session()->has('user_location'))
                <?php
                    // Ambil data lokasi dari Session
                    $location = session('user_location');
                    $lat = round($location['lat'], 4);
                    $lon = round($location['lon'], 4);
                    // Catatan: Jika Anda telah mengimplementasikan Reverse Geocoding, tampilkan nama alamat di sini
                ?>
                <div class="d-flex flex-column">
                    <small class="text-muted" style="font-size: 0.75rem;">Mengantar Ke:</small>
                    <div class="fw-bold text-dark">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> 
                        Lokasi Terdeteksi (Lat: {{ $lat }}, Lon: {{ $lon }})
                    </div>
                </div>
            @else
                <div class="d-flex flex-column">
                    <small class="text-muted" style="font-size: 0.75rem;">Lokasi Anda Belum Ditetapkan</small>
                    <div class="fw-bold text-dark">
                        <i class="bi bi-geo-alt-fill text-secondary me-1"></i> 
                        Tentukan Lokasi Pengiriman
                    </div>
                </div>
            @endif
            
            <button id="btn-get-location" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-repeat"></i> Ubah
            </button>
        </div>
    </div>
    
    <h1 class="mb-4 d-none">Menu Go Mentai</h1> @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-1">Go Mentai Official Store</h5>
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-star-fill text-warning me-1">⭐</i>
                <span class="small text-muted me-3">4.7 (100+ Penilaian)</span> 
                
                <span class="small text-muted"> Tiba dalam 30 Menit</span> 
                <a href="{{ route('order.history') }}" class="btn btn-sm btn-outline-primary ms-auto">Lihat Riwayat Pesanan</a>
            </div>
            
            <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="badge bg-danger">Diskon 50%</span>
                <span class="badge bg-success">Gratis Ongkir</span>
            </div>
        </div>
    </div>
    
    <h2 class="mt-4 mb-3">Pilihan Menu Kami</h2>
    
    <div class="row">
    @foreach ($products as $product)
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
                        <a href="{{ route('product.detail', $product->id) }}" class="btn btn-sm btn-success rounded-circle">+</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
    
@endsection