@extends('layouts.app') 

@section('title', 'Keranjang Belanja')

@section('content')
    <h1 class="mb-4">Keranjang Belanja Anda</h1>

    @if (empty($cart))
        <div class="alert alert-info">
            Keranjang Anda kosong. <a href="{{ url('/') }}" class="alert-link">Yuk, pesan!!</a>
        </div>
    @else
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kuantitas</th>
                            <th>Kustomisasi</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach ($cart as $uuid => $item)
                            @php $grandTotal += $item['subtotal']; @endphp
                            <tr>
                                <td>
                                    <strong>{{ $item['product_name'] }}</strong>
                                    <br><small class="text-muted">Harga Dasar: Rp {{ number_format($item['base_price']) }}</small>
                                </td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>
                                    <ul class="list-unstyled small">
                                    @foreach ($item['customizations'] as $custom)
                                        <li>
                                            * {{ $custom['option_name'] }}: **{{ $custom['value_name'] }}** @if ($custom['price_modifier'] > 0)
                                                (+Rp {{ number_format($custom['price_modifier']) }})
                                            @endif
                                        </li>
                                    @endforeach
                                    </ul>
                                </td>
                                <td><strong>Rp {{ number_format($item['subtotal']) }}</strong></td>
                                <td>
                                    <form action="{{ route('cart.remove', $uuid) }}" method="POST">
                                        @csrf
                                        @method('POST') <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center p-3 border rounded bg-light">
            <h2>GRAND TOTAL:</h2>
            <h2 class="text-danger">Rp {{ number_format($grandTotal) }}</h2>
        </div>

        <div class="text-end mt-3">
            <a href="{{ route('checkout.form') }}" class="btn btn-success btn-lg">
                Lanjutkan ke Pembayaran & Pengiriman
            </a>
        </div>
    @endif
@endsection