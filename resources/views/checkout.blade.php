@extends('layouts.app') 

@section('title', 'Konfirmasi Pesanan')

@section('content')
    <h1 class="mb-4">Konfirmasi Pesanan</h1>

    {{-- Pesan Error & Validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <h4 class="alert-heading">⚠️ Pesanan Gagal Diproses!</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-warning mb-4">
            <h4 class="alert-heading">🚨 Terjadi Kegagalan Proses</h4>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    
    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Informasi Pengiriman
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Edit Nama --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label small text-muted fw-bold">Nama Penerima</label>
                        <input type="text" name="recipient_name" class="form-control" 
                               value="{{ old('recipient_name', Auth::user()->name) }}" required>
                    </div>
                    {{-- Edit Nomor Telepon --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label small text-muted fw-bold">Nomor Telepon</label>
                        <input type="text" name="recipient_phone" class="form-control" 
                               value="{{ old('recipient_phone', Auth::user()->phone) }}" required>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">Alamat Lengkap / Lokasi GPS</label>
                    <input 
                        type="text" id="address" name="address_text" class="form-control mb-2" 
                        placeholder="Masukkan alamat pengiriman..." required 
                        value="{{ session('user_location')['address'] ?? old('address_text') }}"
                    >
                    <button type="button" onclick="calculateDistanceReal()" class="btn btn-sm btn-outline-danger">Cek Jarak & Biaya</button>
                    <p id="status-check" class="small mt-2 loading">Status: Silakan masukkan alamat.</p>
                </div>

                {{-- Tambah Catatan --}}
                <div class="mb-0">
                    <label class="form-label small text-muted fw-bold">Catatan untuk Driver (Opsional)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Pagar hitam, titip di satpam, atau jangan terlalu pedas..."></textarea>
                </div>

                {{-- Hidden Inputs --}}
                <input type="hidden" name="lat" id="lat_input" value="{{ $userLocation['lat'] ?? old('lat') }}">
                <input type="hidden" name="lon" id="lon_input" value="{{ $userLocation['lon'] ?? old('lon') }}">
                <input type="hidden" name="delivery_fee" id="fee_input" value="{{ $deliveryFee ?? 0 }}">
                <input type="hidden" name="distance" id="distance_input" value="{{ $distance ?? 0 }}">
                <input type="hidden" name="eta" id="eta_input">
            </div>
        </div>
        
        {{-- Opsi Pengiriman --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Opsi Pengiriman <span class="small text-muted float-end">Jarak: <span id="distance-display">--</span></span>
            </div>
            <div class="card-body">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="shipping_option" id="priority" value="Priority">
                    <label class="form-check-label" for="priority">
                        Prioritas (< 30 menit) <small class="text-danger">Tambah Rp 1.000</small>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="shipping_option" id="standar" value="Standar" checked>
                    <label class="form-check-label" for="standar">
                        Standar - <span id="eta-display">--</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Ringkasan Pesanan --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Pesanan Anda ({{ count(session('cart', [])) }} Menu)
                <a href="{{ route('cart.index') }}" class="btn btn-link btn-sm float-end p-0">Ubah</a>
            </div>
            <div class="card-body">
                @foreach (session('cart', []) as $item)
                    <div class="d-flex justify-content-between mb-1">
                        <small>{{ $item['product_name'] }} ({{ $item['quantity'] }}x)</small>
                        <small>Rp {{ number_format($item['subtotal']) }}</small>
                    </div>
                @endforeach
            </div>
        </div>
        
        {{-- Pembayaran & Total --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Pilihan Pembayaran
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="Transfer Bank" id="pay_transfer" required>
                        <label class="form-check-label" for="pay_transfer">Transfer Bank</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="COD" id="pay_cod" required>
                        <label class="form-check-label" for="pay_cod">COD (Bayar di Tempat)</label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mb-1 text-muted">
                    <span>Total Produk:</span>
                    <span>Rp {{ number_format($totalBarang = array_sum(array_column(session('cart', []), 'subtotal'))) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-muted">
                    <span>Ongkos Kirim:</span>
                    <span id="delivery-fee-summary">Menghitung...</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total Bayar:</span>
                    <span class="text-danger" id="final-total-summary">Menghitung...</span>
                </div>
            </div>
        </div>

        <button type="submit" id="submit-button" class="btn btn-danger btn-lg w-100 mb-5" disabled>
            Buat Pesanan Sekarang
        </button>
    </form>

    <script>
    function calculateDistanceReal() {
        // Ambil data dari variabel PHP yang dikirim Controller
        const distanceKm = {{ $distance ?? 0 }}; 
        const deliveryFee = {{ $deliveryFee ?? 0 }};
        const totalBarang = {{ array_sum(array_column(session('cart', []), 'subtotal')) }};
        
        const KECEPATAN_KURIR_KMH = 30;
        const etaMinutes = Math.round((distanceKm / KECEPATAN_KURIR_KMH) * 60) + 15; // +15 menit waktu masak
        const finalTotal = totalBarang + deliveryFee;

        // Update Hidden Inputs
        document.getElementById('distance_input').value = distanceKm;
        document.getElementById('eta_input').value = etaMinutes;
        document.getElementById('fee_input').value = deliveryFee;

        // Update Tampilan UI
        document.getElementById('distance-display').innerHTML = `${distanceKm} km`;
        document.getElementById('eta-display').innerHTML = `Sekitar ${etaMinutes} menit`;
        document.getElementById('delivery-fee-summary').innerHTML = `Rp ${deliveryFee.toLocaleString('id-ID')}`;
        document.getElementById('final-total-summary').innerHTML = `Rp ${finalTotal.toLocaleString('id-ID')}`;

        // Aktifkan tombol submit
        document.getElementById('submit-button').disabled = false;
        document.getElementById('status-check').innerHTML = `<span class="text-success fw-bold">Status: Perhitungan selesai (${distanceKm} km).</span>`;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Jika data lokasi sudah ada di session, langsung jalankan perhitungan
        if (document.getElementById('lat_input').value && document.getElementById('lon_input').value) {
            calculateDistanceReal();
        }
    });
    </script>
@endsection