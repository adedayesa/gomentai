@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark">Dashboard Driver</h1>
        <span class="badge bg-success shadow-sm">Status: Aktif</span>
    </div>

    {{-- Statistik Dashboard --}}
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="opacity-75">Pesanan Baru</h6>
                    <h2 class="fw-bold mb-0">{{ $newOrdersCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-warning text-dark shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="opacity-75">Sedang Kirim</h6>
                    <h2 class="fw-bold mb-0">{{ $shippingCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="opacity-75">Total Selesai</h6>
                    <h2 class="fw-bold mb-0">{{ $completedCount }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL TUGAS PENGIRIMAN --}}
    <div class="card mt-3 shadow-sm border-0">
        <div class="card-header bg-dark text-white fw-bold">
            <i class="fas fa-truck me-2"></i> Daftar Tugas Pengiriman
        </div>
        <div class="card-body p-0">
            @if(session('success')) <div class="alert alert-success m-3 small">{{ session('success') }}</div> @endif

            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-uppercase text-muted">
                            <th class="px-3">ID Order</th>
                            <th>Pelanggan & HP</th>
                            <th>Total</th>
                            <th>Alamat & Catatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="px-3 fw-bold">#{{ $order->id }}</td>
                            <td>
                                <strong>{{ $order->recipient_name ?? ($order->user->name ?? 'Pelanggan') }}</strong><br>
                                <small class="text-muted">{{ $order->phone ?? ($order->user->phone ?? '-') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark mb-1">LUNAS</span><br>
                                <small class="fw-bold text-dark">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</small>
                            </td>
                            <td>
                                @php 
                                    // DIPERBAIKI: Menggunakan address_text sesuai database kamu
                                    $alamatTugas = $order->address ?? ($order->delivery->address_text ?? 'Alamat Kosong');
                                @endphp
                                <div class="small fw-bold text-truncate" style="max-width: 200px;">{{ $alamatTugas }}</div>
                                <small class="text-danger italic">"{{ $order->notes ?? 'Tidak ada catatan' }}"</small>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($alamatTugas) }}" 
                                       target="_blank" class="btn btn-danger btn-sm shadow-sm">
                                        <i class="fas fa-map-marked-alt"></i> Maps
                                    </a>

                                    @php
                                        $rawPhone = $order->phone ?? ($order->user->phone ?? '');
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
                                        if (str_starts_with($cleanPhone, '0')) { $cleanPhone = '62' . substr($cleanPhone, 1); }
                                    @endphp
                                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn btn-success btn-sm shadow-sm">
                                        <i class="fab fa-whatsapp"></i> WA
                                    </a>
                                    
                                    <form action="{{ route('driver.update_status', $order->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        @if($order->status === 'Ready for Pickup')
                                            <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">Pick</button>
                                        @else
                                            <button type="submit" class="btn btn-warning btn-sm text-white px-3 shadow-sm">Done</button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada tugas hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="card mt-4 shadow-sm border-0 mb-5">
        <div class="card-header bg-secondary text-white fw-bold">
            <i class="fas fa-history me-1"></i> Riwayat Pengiriman Hari Ini
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="px-3 py-2">ID</th>
                            <th>Pelanggan & HP</th>
                            <th>Alamat</th>
                            <th class="text-end px-3">Waktu Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($completedOrders as $done)
                        <tr>
                            <td class="px-3 py-2 text-muted">#{{ $done->id }}</td>
                            <td>
                                <strong>{{ $done->recipient_name ?? ($done->user->name ?? 'Pelanggan') }}</strong><br>
                                <small class="text-muted">{{ $done->phone ?? ($done->user->phone ?? '-') }}</small>
                            </td>
                            <td>
                                {{-- DIPERBAIKI: Menggunakan address_text agar alamat muncul --}}
                                <div class="small text-truncate" style="max-width: 250px;">
                                    {{ $done->address ?? ($done->delivery->address_text ?? 'Alamat Kosong') }}
                                </div>
                            </td>
                            <td class="text-end px-3 small text-muted">
                                {{ $done->updated_at->format('H:i') }} <i class="fas fa-check-circle text-success ms-1"></i>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3 text-muted small">Belum ada riwayat hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * DASHBOARD DRIVER AUTO-REFRESH
     * Menyegarkan halaman setiap 30 detik untuk memantau tugas baru.
     */
    setTimeout(function(){
        // Pada halaman daftar (list), kita biasanya selalu ingin refresh 
        // agar Driver tahu jika ada pesanan baru masuk ke dalam daftar mereka.
        window.location.reload();
    }, 30000); // 30000 milidetik = 30 detik
</script>
@endsection