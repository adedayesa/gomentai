@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya')

@section('content')
<div class="container">
    <h1 class="mb-4">📦 Riwayat Pesanan</h1>

    @if ($orders->isEmpty())
        <div class="alert alert-info">
            Anda belum memiliki riwayat pesanan. Silakan mulai berbelanja!
        </div>
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">Mulai Pesan</a>
    @else
        <div class="list-group">
            @foreach ($orders as $order)
                <a href="{{ route('order.status', $order) }}" class="list-group-item list-group-item-action mb-3 shadow-sm rounded">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Pesanan #{{ $order->id }}</h5>
                        {{-- Menampilkan status Order utama --}}
                        <small class="badge bg-{{ $order->status == 'Selesai' ? 'success' : 'warning' }} text-white p-2">{{ $order->status }}</small>
                    </div>
                    <p class="mb-1">Tanggal Pesan: {{ $order->created_at->format('d M Y H:i') }}</p>
                    <p class="mb-1">Total Bayar: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
                    <small>Klik untuk melihat detail dan status pembayaran.</small>
                </a>
            @endforeach
        </div>

        {{-- Tambahan Tombol Kembali --}}
        <div class="mt-4">
            <a href="{{ url('/') }}" class="btn btn-secondary w-100 py-2 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>
    @endif
</div>
@endsection