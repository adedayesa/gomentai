@extends('layouts.app') 

@section('title', 'Dashboard Admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4 fw-bold text-dark">Dashboard Admin Go Mentai</h1>
                
                <div class="alert alert-success shadow-sm border-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Selamat datang, <strong>Admin</strong>! Akses berhasil. Anda dapat mulai mengelola operasional toko.
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #dc3545 !important;">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Produk & Menu</h5>
                        <h2 class="fw-bold">{{ \App\Models\Product::count() }}</h2>
                        <p class="card-text text-secondary">Kelola daftar makanan, harga, dan stok.</p>
                        <a href="{{ route('menus.index') }}" class="btn btn-danger w-100">
                            Kelola Menu
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #0d6efd !important;">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Pesanan Pelanggan</h5>
                        <h2 class="fw-bold">Lihat Semua</h2>
                        <p class="card-text text-secondary">Pantau pesanan masuk dan status pengiriman.</p>
                        <a href="{{ route('admin.orders') }}" class="btn btn-primary w-100">
                            Lihat Semua Pesanan
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #ffc107 !important;">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Manajemen User</h5>
                        <h2 class="fw-bold">{{ \App\Models\User::count() }}</h2>
                        <p class="card-text text-secondary">Kelola data Customer dan Driver.</p>
                        <a href="{{ route('users.index') }}" class="btn btn-warning w-100 text-white">
                            Kelola User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection