@extends('layouts.app')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Dashboard Admin
            </a>
            <h1 class="h3 fw-bold text-dark mb-0">Daftar Pesanan Masuk</h1>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">ID</th>
                            <th>Waktu Pesan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th class="text-center">Status Order</th>
                            <th class="text-center">Status Bayar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="px-4 fw-bold text-muted">#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d M H:i') }}</td>
                                <td>{{ $order->recipient_name ?? $order->customer->name ?? 'Tamu' }}</td>
                                <td class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-primary px-3 py-2 text-uppercase" style="font-size: 0.7rem;">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-warning text-dark px-3 py-2 text-uppercase" style="font-size: 0.7rem;">
                                        {{ $order->payment->validation_status ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="text-center px-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Tombol Detail --}}
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info text-white px-3 shadow-sm">Detail</a>

                                        {{-- TOMBOL WHATSAPP (Ada Ikon & Tulisan WA) --}}
                                        @php
                                            $phone = $order->phone ?? ($order->customer->phone ?? null);
                                            if($phone) {
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                                $waUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode("Halo " . ($order->recipient_name) . ", saya Admin Go Mentai ingin mengonfirmasi pesanan #" . $order->id);
                                            }
                                        @endphp
                                        @if($phone)
                                            <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-success px-3 shadow-sm text-white">
                                                <i class="fab fa-whatsapp"></i> WA
                                            </a>
                                        @endif

                                        {{-- TOMBOL CETAK (Ada Ikon & Tulisan Cetak) --}}
                                        <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn btn-sm btn-primary px-3 shadow-sm">
                                            <i class="fas fa-print"></i> Cetak
                                        </a>

                                        @if($order->status == 'Waiting Confirmation' || $order->status == 'pending')
                                            <form action="{{ route('admin.orders.confirm_payment', $order->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success px-3 shadow-sm">Konfirmasi</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(session('open_print'))
<script>window.open("{{ session('open_print') }}", '_blank');</script>
@endif
@endsection