@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Dashboard Admin
            </a>
            <h2 class="fw-bold text-dark d-block">Daftar Menu Go Mentai</h2>
        </div>
        <a href="{{ route('menus.create') }}" class="btn btn-danger shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Menu
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Gambar</th>
                            <th class="py-3">Nama Menu</th>
                            <th class="py-3">Harga</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="px-4 align-middle">
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         style="width: 70px; height: 70px; object-fit: cover;" 
                                         class="rounded shadow-sm">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="fw-bold text-dark">{{ $product->name }}</div>
                                <small class="text-muted">{{ $product->category->name ?? 'Tanpa Kategori' }}</small>
                            </td>
                            <td class="align-middle fw-bold text-danger">
                                Rp {{ number_format($product->base_price, 0, ',', '.') }}
                            </td>
                            <td class="align-middle text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('menus.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('menus.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Belum ada menu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection