@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('menus.index') }}" class="btn btn-sm btn-light border shadow-sm">
            <i class="fas fa-chevron-left me-1"></i> Kembali ke Daftar Menu
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Edit Menu: {{ $menu->name }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Menu</label>
                            <input type="text" name="name" value="{{ $menu->name }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Harga Dasar (Rp)</label>
                            <input type="number" name="base_price" value="{{ $menu->base_price }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto Saat Ini</label>
                            <div class="mb-2">
                                <img src="{{ asset($menu->image) }}" class="rounded" style="width: 100px;">
                            </div>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection