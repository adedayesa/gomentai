@extends('layouts.app')

<?php
    // Logika penentuan status judul dan deteksi metode
    $orderStatus = $order->status;
    $isCOD = (strtolower($order->payment_method) === 'cod');

    if (!$isCOD) {
        if ($order->payment->validation_status === 'Waiting Upload') {
            $orderStatus = 'Menunggu Pembayaran';
        } elseif (in_array($order->payment->validation_status, ['Pending Admin', 'Proof Uploaded'])) {
            $orderStatus = 'Menunggu Verifikasi';
        } elseif ($order->payment->validation_status === 'Rejected') {
            $orderStatus = 'Pembayaran Ditolak';
        }
    }
?>

@section('title', 'Status Pesanan #' . $order->id)

@section('content')
<style>
    /* Tracking UI */
    .status-steps { display: flex; justify-content: space-between; position: relative; padding: 20px 0; }
    .step { text-align: center; flex: 1; position: relative; }
    .step p { font-size: 28px; margin: 0; margin-bottom: 5px; }
    .step small { font-weight: bold; display: block; font-size: 11px; letter-spacing: 1px; }
    .active-status { color: #e67e22; transform: scale(1.1); transition: 0.3s; }
    .inactive-status { color: #dee2e6; filter: grayscale(100%); opacity: 0.6; }
    
    /* Utility */
    .total-amount { font-size: 24px; color: #e67e22; font-weight: 800; }
    .order-header { border-left: 5px solid #e67e22; padding-left: 15px; }
    .bank-box { background: #f8f9fa; border-radius: 12px; padding: 15px; border: 1px dashed #ced4da; }
</style>

<div class="container py-4">
    <div class="order-header mb-4">
        <h2 class="fw-bold mb-0">Pesanan #{{ $order->id }}</h2>
        <div class="d-flex gap-2 mt-1">
            <span class="badge rounded-pill bg-light text-dark border">{{ $orderStatus }}</span>
            @if($isCOD)
                <span class="badge rounded-pill bg-info text-white">Metode: COD</span>
            @endif
        </div>
    </div>

    {{-- TOMBOL BATALKAN PESANAN --}}
    {{-- Tombol muncul selama status BUKAN Preparing, Ready, Delivery, atau Delivered --}}
    @if(!in_array($order->status, ['Preparing', 'Ready for Pickup', 'On Delivery', 'Delivered', 'Cancelled']))
        <div class="card border-0 shadow-sm mb-4 bg-light">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold text-danger">Ingin membatalkan pesanan?</h6>
                    <small class="text-muted">Pesanan dapat dibatalkan sebelum masuk tahap persiapan dapur.</small>
                </div>
                <form action="{{ route('order.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                    @csrf
                    {{-- Tombol untuk memicu Modal --}}
                    <button type="button" class="btn btn-danger btn-sm px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                    </button>

                    {{-- Struktur Modal Alasan Pembatalan --}}
                    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cancelModalLabel">Alasan Pembatalan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('order.cancel', $order->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p>Mohon beritahu kami alasan Anda membatalkan pesanan ini:</p>
                                <textarea name="cancel_reason" class="form-control" rows="3" placeholder="Contoh: Ingin mengganti menu, salah alamat, dll..." required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Konfirmasi Pembatalan</button>
                            </div>
                        </form>
                        </div>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- 1. LOGIKA PEMBAYARAN TRANSFER --}}
    @if (!$isCOD)
        @if ($order->payment->validation_status === 'Rejected')
            <div class="card shadow-sm mb-4 border-0 bg-danger bg-opacity-10 text-center py-3">
                <h5 class="text-danger fw-bold">Pembayaran Ditolak</h5>
                <p class="small">Alasan: {{ $order->payment->rejection_notes ?? 'Bukti tidak jelas.' }}</p>
                <form action="{{ route('order.upload_proof', $order) }}" method="POST" enctype="multipart/form-data" class="px-4">
                    @csrf
                    <div class="input-group">
                        <input type="file" name="payment_proof" class="form-control" required>
                        <button type="submit" class="btn btn-danger">Upload Lagi</button>
                    </div>
                </form>
            </div>
        @elseif ($order->payment->validation_status === 'Waiting Upload')
            <div class="card shadow-sm border-0 mb-4 text-center p-4">
                <p class="text-muted mb-1">Total Bayar:</p>
                <div class="total-amount mb-4">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                <div class="bank-box mb-3 text-start">
                    <small class="text-muted d-block">Bank BRI:</small>
                    <strong class="fs-5">1234-567-890</strong><br>
                    <small>a.n Go Mentai Official</small>
                </div>
                <form action="{{ route('order.upload_proof', $order) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="payment_proof" class="form-control mb-3" required>
                    <button type="submit" class="btn btn-warning w-100 text-white fw-bold py-2">Konfirmasi Pembayaran</button>
                </form>
            </div>
        @elseif (in_array($order->payment->validation_status, ['Pending Admin', 'Proof Uploaded']))
            <div class="card shadow-sm border-0 mb-4 text-center py-5">
                <div class="spinner-grow text-warning mb-3"></div>
                <h5>Menunggu Verifikasi</h5>
                <p class="text-muted px-4 small">Admin sedang mengecek bukti bayarmu. Tracking akan muncul setelah disetujui.</p>
            </div>
        @endif
    @endif

    {{-- 2. TAMPILAN TRACKING --}}
    @if ($isCOD || $order->payment->validation_status === 'Confirmed')
        <div class="card shadow-sm border-0 mb-4">
            <div class="{{ $isCOD ? 'bg-info' : 'bg-success' }} py-2 px-4 text-white small d-flex justify-content-between">
                <span><i class="bi bi-truck me-1"></i> {{ $isCOD ? 'Pesanan Sedang Diproses (COD)' : 'Pembayaran Berhasil' }}</span>
                <span class="fw-bold text-uppercase">Status</span>
            </div>
            <div class="card-body p-4">
                <div class="status-steps">
                    <div class="step {{ in_array($order->status, ['Preparing', 'Ready for Pickup', 'On Delivery', 'Delivered']) ? 'active-status' : 'inactive-status' }}">
                        <p>🍳</p><small>PREPARING</small>
                    </div>
                    <div class="step {{ in_array($order->status, ['Ready for Pickup', 'On Delivery', 'Delivered']) ? 'active-status' : 'inactive-status' }}">
                        <p>🛍️</p><small>READY</small>
                    </div>
                    <div class="step {{ in_array($order->status, ['On Delivery', 'Delivered']) ? 'active-status' : 'inactive-status' }}">
                        <p>🛵</p><small>DELIVERY</small>
                    </div>
                    <div class="step {{ $order->status === 'Delivered' ? 'active-status' : 'inactive-status' }}">
                        <p>✅</p><small>DONE</small>
                    </div>
                </div>

                @if($order->status === 'On Delivery')
                    <div class="mt-4 p-3 bg-light rounded d-flex justify-content-between align-items-center border">
                        <div>
                            <small class="text-muted d-block">Kurir:</small>
                            <strong>{{ $order->delivery->driver_name ?? 'Kurir Go Mentai' }}</strong>
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->delivery->driver_phone ?? '') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                            <i class="bi bi-whatsapp"></i> Chat
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- 3. REVIEW --}}
    @if($order->status === 'Delivered' && !$order->rating)
        <div class="card shadow-sm border-0 border-top border-warning border-4 mb-4">
            <div class="card-body text-center p-4">
                <h5 class="fw-bold mb-3">Pesanan Sampai! Beri Nilai?</h5>
                <form action="{{ route('order.review', $order->id) }}" method="POST">
                    @csrf
                    <select name="rating" class="form-select form-select-lg mb-3 mx-auto text-center" style="max-width: 200px;">
                        <option value="5">⭐⭐⭐⭐⭐</option>
                        <option value="4">⭐⭐⭐⭐</option>
                        <option value="3">⭐⭐⭐</option>
                    </select>
                    <button type="submit" class="btn btn-warning w-100 text-white fw-bold shadow-sm">Kirim Review</button>
                </form>
            </div>
        </div>
    @endif

    {{-- 4. RINGKASAN PESANAN --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 fw-bold border-0">
            <i class="bi bi-receipt me-2"></i>Ringkasan Pesanan
        </div>
        <div class="card-body pt-0">
            @foreach($order->items as $item)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">{{ $item->quantity }}x {{ $item->product->name }}</span>
                    <span class="fw-bold">Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">Total Pembayaran</span>
                <span class="total-amount fs-5">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-center">
        <a href="{{ url('/') }}" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
    </div>
</div>

<script>
    // Cek apakah modal pembatalan sedang dibuka
    let isCancelModalOpen = false;

    document.addEventListener('DOMContentLoaded', function() {
        const cancelModal = document.getElementById('cancelModal');
        if (cancelModal) {
            cancelModal.addEventListener('show.bs.modal', () => isCancelModalOpen = true);
            cancelModal.addEventListener('hidden.bs.modal', () => isCancelModalOpen = false);
        }
    });

    function autoRefresh() {
        setTimeout(function(){
            @if($order->status !== 'Delivered' && $order->status !== 'Cancelled')
                if (!isCancelModalOpen) {
                    window.location.reload();
                } else {
                    autoRefresh(); // Cek lagi nanti jika modal ditutup
                }
            @endif
        }, 30000);
    }
    
    autoRefresh();
</script>
@endsection