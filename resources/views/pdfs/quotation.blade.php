<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Quotation</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 24px; color: #ea580c; margin: 0; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { vertical-align: top; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th { background: #f97316; color: white; padding: 8px 12px; text-align: left; font-size: 11px; }
        table.items td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
        table.items tr:nth-child(even) td { background: #f9fafb; }
        .total td { font-weight: bold; padding-top: 12px; }
        .footer { text-align: center; color: #9ca3af; font-size: 10px; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>QUOTATION</h1>
        <p style="color: #6b7280;">{{ $quotation->quotation_number }}</p>
    </div>
    <div class="info">
        <table>
            <tr>
                <td><strong>Pelanggan:</strong><br>{{ $quotation->customer?->name ?? 'N/A' }}<br>{{ $quotation->customer?->phone ?? '' }}</td>
                <td class="text-right">
                    <strong>Tanggal:</strong> {{ $quotation->quotation_date?->format('d/m/Y') ?? $quotation->created_at->format('d/m/Y') }}<br>
                    <strong>Berlaku Sampai:</strong> {{ $quotation->valid_until?->format('d/m/Y') ?? '-' }}<br>
                    <strong>Status:</strong> {{ $quotation->status ?? 'Draft' }}<br>
                    @if($quotation->currency)<strong>Mata Uang:</strong> {{ $quotation->currency->code }}@endif
                </td>
            </tr>
        </table>
    </div>
    @if($quotation->notes)
    <p style="margin-bottom: 12px;"><strong>Catatan:</strong> {{ $quotation->notes }}</p>
    @endif
    <table class="items">
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th>Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->product?->name ?? $item->description ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($item->quantity, 0) }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="4" class="text-right">Total</td>
                <td class="text-right">Rp {{ number_format($quotation->total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @if($quotation->terms)
    <p style="margin-bottom: 12px;"><strong>Syarat & Ketentuan:</strong><br>{{ $quotation->terms }}</p>
    @endif
    <div class="footer">
        <p>Dicetak pada {{ now()->format('d/m/Y H:i') }} | MoneyKu</p>
    </div>
</body>
</html>
