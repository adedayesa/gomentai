@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        
        {{-- TOMBOL CETAK MANUAL --}}
        <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn btn-sm btn-info text-white shadow-sm">
            <i class="fas fa-print me-1"></i> Cetak Struk
        </a>
    </div>

    <h1 class="mb-4">Detail Pesanan #{{ $order->id }}</h1>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Rincian Item (Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }})</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($order->items as $item)
                            <li class="list-group-item">
                                <strong>{{ $item->product->name }} ({{ $item->quantity }}x)</strong>
                                <small class="float-end">Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</small><br>
                                @php $customs = json_decode($item->customization_details, true); @endphp
                                @if ($customs)
                                    <small>Kustomisasi:</small>
                                    <ul>
                                        @foreach ($customs as $custom)
                                            <li class="small">{{ $custom['option_name'] }}: {{ $custom['value_name'] }} (+Rp {{ number_format($custom['price_modifier']) }})</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark fw-bold">
                    Status Pembayaran: {{ $order->payment->validation_status ?? 'Pending' }}
                </div>
                <div class="card-body">
                    @if ($order->payment && $order->payment->proof_image_path)
                        <p class="fw-bold">Bukti Transfer diunggah oleh pelanggan:</p>
                        
                        <div class="text-center mb-3">
                            <a href="{{ asset($order->payment->proof_image_path) }}" target="_blank">
                                <img src="{{ asset($order->payment->proof_image_path) }}" 
                                    class="img-fluid img-thumbnail" 
                                    style="max-height: 400px; cursor: zoom-in;" 
                                    alt="Bukti Pembayaran">
                            </a>
                            <br>
                            <small class="text-muted">Klik gambar untuk melihat ukuran penuh</small>
                        </div>

                        <div class="d-flex gap-2">
                            <form action="{{ route('admin.orders.confirm_payment', $order) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 fw-bold">✅ Konfirmasi (Valid)</button>
                            </form>

                            <button type="button" class="btn btn-danger flex-grow-1 fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                ❌ Tolak (Salah)
                            </button>
                        </div>

                        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.orders.reject_payment', $order) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Alasan Penolakan Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label text-dark">Tulis alasan kenapa bukti ini ditolak:</label>
                                            <textarea name="rejection_notes" class="form-control" rows="3" placeholder="Contoh: Foto buram atau nominal tidak sesuai" required></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Kirim & Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted italic">Bukti transfer belum diunggah oleh pelanggan.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white fw-bold">Info Pengiriman</div>
                <div class="card-body text-dark">
                    <p>Status Order: <strong>{{ $order->status }}</strong></p>
                    <p>Alamat: {{ $order->delivery->address_text ?? $order->address }}</p>
                    <p>Jarak: {{ $order->delivery->distance_km ?? '0' }} km</p>
                    <p>ETA: {{ $order->delivery->eta_minutes ?? '0' }} menit</p>
                    <p>Biaya Kirim: Rp {{ number_format($order->delivery->delivery_fee ?? 0, 0, ',', '.') }}</p>
                    
                    {{-- TAMBAHAN BOX CATATAN PELANGGAN --}}
                    <div class="mt-3 p-3 bg-light border-start border-4 border-warning shadow-sm">
                        <p class="mb-1 fw-bold text-dark"><i class="fas fa-sticky-note me-1 text-warning"></i> Catatan Pelanggan:</p>
                        <p class="mb-0 text-danger fw-bold" style="font-style: italic;">
                            {{ $order->notes ? '"' . $order->notes . '"' : 'Tidak ada catatan khusus' }}
                        </p>
                    </div>

                    <hr>
                    <h6 class="mt-3 fw-bold">Update Status Order</h6>
                    <form action="{{ route('admin.orders.update_status', $order) }}" method="POST">
                        @csrf
                        <select name="status" class="form-select mb-2">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold shadow-sm">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('open_print'))
    <script>
        window.open("{{ session('open_print') }}", '_blank');
    </script>
    @endif

<script>
    // Variabel untuk melacak apakah Admin sedang membuka modal penolakan
    let isModalOpen = false;

    // Menunggu halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const rejectModal = document.getElementById('rejectModal');
        if (rejectModal) {
            // Jika modal dibuka, set jadi true
            rejectModal.addEventListener('show.bs.modal', function () {
                isModalOpen = true;
            });
            // Jika modal ditutup, set kembali jadi false
            rejectModal.addEventListener('hidden.bs.modal', function () {
                isModalOpen = false;
            });
        }
    });

    // Fungsi untuk menjalankan reload otomatis
    function autoRefresh() {
        setTimeout(function(){
            @if($order->status !== 'Delivered' && $order->status !== 'Cancelled')
                // Hanya reload jika modal TIDAK sedang terbuka
                if (!isModalOpen) {
                    window.location.reload();
                } else {
                    // Jika modal sedang terbuka, tunda reload dan cek lagi dalam 5 detik
                    autoRefresh();
                }
            @endif
        }, 30000); // 30000 ms = 30 detik
    }

    // Jalankan fungsi refresh
    autoRefresh();
</script>
@endsection