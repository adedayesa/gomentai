<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan | Go Mentai</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .tracking-wrapper { background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #eee; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .status-steps { display: flex; justify-content: space-between; position: relative; }
        .step { text-align: center; flex: 1; transition: 0.3s; }
        .step p { font-size: 28px; margin: 0; }
        .step small { font-weight: bold; display: block; margin-top: 5px; font-size: 10px; }
        .active-status { color: #e67e22; transform: scale(1.1); }
        .inactive-status { color: #ccc; filter: grayscale(100%); opacity: 0.5; }
        .btn-wa { display: inline-block; background-color: #25D366; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 15px; }
        .review-card { margin-top: 30px; padding: 20px; border: 2px solid #e67e22; border-radius: 10px; background: #fffcf9; text-align: center; }
        .star-rating { margin: 15px 0; }
        .star-rating select { padding: 10px; border-radius: 5px; border: 1px solid #e67e22; outline: none; }
        textarea { width: 100%; height: 80px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-top: 10px; resize: none; }

        /* Tambahan Style untuk Tombol Kembali */
        .btn-back {
            display: block;
            width: 100%;
            text-align: center;
            background-color: #e67e22;
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: background 0.3s ease;
        }
        .btn-back:hover {
            background-color: #d35400;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <header class="header-banner">
            <h1>Pesanan #{{ $order->id }} - 
                @if($order->payment->validation_status === 'Confirmed')
                    {{ $order->status == 'Delivered' ? 'Selesai' : 'Diproses' }}
                @else
                    Konfirmasi Pembayaran
                @endif
            </h1>
        </header>

        <div class="content-wrapper">
            {{-- 1. TRACKING STATUS --}}
            @if($order->payment->validation_status === 'Confirmed')
                <section class="tracking-section">
                    <div class="tracking-wrapper">
                        <h2 style="text-align: center; margin-bottom: 25px;">Pelacakan Pesanan</h2>
                        <div class="status-steps">
                            {{-- Step 1: Preparing --}}
                            <div class="step {{ in_array($order->status, ['Preparing', 'Ready for Pickup', 'On Delivery', 'Delivered']) ? 'active-status' : 'inactive-status' }}">
                                <p>🍳</p><small>PREPARING</small>
                            </div>
                            {{-- Step 2: Ready --}}
                            <div class="step {{ in_array($order->status, ['Ready for Pickup', 'On Delivery', 'Delivered']) ? 'active-status' : 'inactive-status' }}">
                                <p>🛍️</p><small>READY</small>
                            </div>
                            {{-- Step 3: Delivery --}}
                            <div class="step {{ in_array($order->status, ['On Delivery', 'Delivered']) ? 'active-status' : 'inactive-status' }}">
                                <p>🛵</p><small>DELIVERY</small>
                            </div>
                            {{-- Step 4: Done --}}
                            <div class="step {{ $order->status === 'Delivered' ? 'active-status' : 'inactive-status' }}">
                                <p>✅</p><small>DONE</small>
                            </div>
                        </div>

                        @if($order->status === 'On Delivery')
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed #eee; text-align: center;">
                                <p>Pesananmu sedang diantar oleh: <strong>{{ $order->delivery->driver_name ?? 'Driver Go Mentai' }}</strong></p>
                                @if(isset($order->delivery->driver_phone))
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->delivery->driver_phone) }}?text=Halo%2C%20saya%20pemesan%20ID%20%23{{ $order->id }}%20mau%20tanya%20posisi." 
                                       target="_blank" class="btn-wa">💬 Chat Driver</a>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- 2. FORM REVIEW --}}
                    @if($order->status === 'Delivered' && !$order->review)
                        <div class="review-card">
                            <h3>Pesanan Telah Sampai! 🍱</h3>
                            <p>Bantu kami berkembang dengan memberikan ulasan.</p>
                            <form action="{{ route('order.review', $order->id) }}" method="POST">
                                @csrf
                                <div class="star-rating">
                                    <select name="rating" required>
                                        <option value="">-- Beri Bintang --</option>
                                        <option value="5">⭐⭐⭐⭐⭐ (Sempurna)</option>
                                        <option value="4">⭐⭐⭐⭐ (Enak)</option>
                                        <option value="3">⭐⭐⭐ (Biasa Saja)</option>
                                        <option value="2">⭐⭐ (Perlu Perbaikan)</option>
                                        <option value="1">⭐ (Buruk)</option>
                                    </select>
                                </div>
                                <textarea name="comment" placeholder="Ceritakan pengalamanmu..."></textarea>
                                <button type="submit" class="btn-submit" style="width: 100%; margin-top: 15px;">Kirim Ulasan</button>
                            </form>
                        </div>
                    @elseif($order->status === 'Delivered' && $order->review)
                        <div class="alert alert-success mt-4 text-center">
                            <strong>Terima kasih!</strong> Ulasan Anda sangat berarti bagi kami.
                        </div>
                    @endif
                </section>

            @else
                {{-- JIKA BELUM BAYAR --}}
                <section class="payment-section">
                    <div class="payment-card">
                        <h3>Transfer ke {{ $accountDetails['bank_name'] }}:</h3>
                        <p>No. Rekening: <strong>{{ $accountDetails['account_number'] }}</strong></p>
                        <p>Total: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
                    </div>
                    <form action="{{ route('payment.upload', $order->tracking_token) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Unggah Bukti Transfer</label>
                            <input type="file" name="proof_of_payment" required>
                        </div>
                        <button type="submit" class="btn-submit">Kirim Bukti Pembayaran</button>
                    </form>
                </section>
            @endif

            <hr class="divider">

            <section class="summary-section">
                <h2>Ringkasan Pesanan</h2>
                <div class="summary-card">
                    @foreach ($order->items as $item)
                        <div class="item-row">
                            <span>{{ $item->product->name }} ({{ $item->quantity }}x)</span>
                            <span>Rp{{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="final-price">
                        <strong>Total: Rp{{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </section>

            {{-- TOMBOL KEMBALI (Hanya muncul jika status Selesai) --}}
            @if($order->status === 'Delivered')
                <a href="/" class="btn-back">Kembali ke Beranda</a>
            @endif

        </div>
    </div>

    <script>
        setTimeout(function(){
            @if($order->status !== 'Delivered')
                window.location.reload();
            @endif
        }, 30000);
    </script>
</body>
</html>