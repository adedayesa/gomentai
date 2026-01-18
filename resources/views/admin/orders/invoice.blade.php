<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Struk #{{ $order->id }}</title>
    <style>
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 280px; 
            margin: 0 auto; 
            padding: 10px; 
            font-size: 12px; 
            color: #000;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        .double-line { border-top: 1px solid #000; margin: 2px 0 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        .item-row td { vertical-align: top; padding: 2px 0; }
        .price-col { text-align: right; white-space: nowrap; }
        .notes { font-size: 11px; font-style: italic; padding-left: 5px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function() { window.close(); }">

    <div class="center">
        <h2 style="margin: 0; font-size: 16px;">GOMENTAI</h2>
        <h1 style="margin: 5px 0; font-size: 24px;">#{{ substr($order->id, -3) }}</h1>
    </div>

    <div class="double-line"></div>

    <table>
        <tr>
            <td>Pelanggan:</td>
            <td align="right">{{ $order->customer->name ?? $order->recipient_name }}</td>
        </tr>
        <tr>
            <td>Waktu Pesan:</td>
            <td align="right">{{ $order->created_at->format('d/m/y H:i') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- Daftar Item Makanan --}}
    <table>
        @foreach($order->items as $item)
            <tr class="item-row">
                <td class="bold">{{ $item->quantity }}x {{ $item->product->name }}</td>
                <td class="price-col bold">Rp{{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @php $customs = json_decode($item->customization_details, true); @endphp
            @if ($customs)
                @foreach ($customs as $custom)
                    <tr><td colspan="2" class="notes">- {{ $custom['option_name'] }}: {{ $custom['value_name'] }}</td></tr>
                @endforeach
            @endif
        @endforeach
    </table>

    <div class="line"></div>

    {{-- Rincian Biaya & Ongkir --}}
    <table>
        @if($order->delivery && $order->delivery->delivery_fee > 0)
        <tr>
            <td>Biaya Kirim</td>
            <td class="price-col">Rp{{ number_format($order->delivery->delivery_fee, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="bold">
            <td style="padding-top: 5px;">TOTAL BAYAR</td>
            <td class="price-col" style="padding-top: 5px;">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Catatan Pelanggan --}}
    @if($order->notes)
    <div class="line"></div>
    <table>
        <tr><td class="bold">Catatan:</td></tr>
        <tr><td class="notes">"{{ $order->notes }}"</td></tr>
    </table>
    @endif

    <div class="line"></div>

    <table>
        <tr><td class="bold">Metode Pembayaran:</td></tr>
        <tr><td>{{ $order->payment_method ?? 'Transfer' }}</td></tr>
    </table>

    <div class="double-line"></div>

    <div class="center" style="margin-top: 10px;">
        *** TERIMA KASIH ***<br>
        Selamat Menikmati!
    </div>

</body>
</html>