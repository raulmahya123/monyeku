<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 24px; color: #ea580c; margin: 0; }
        .header .subtitle { color: #6b7280; font-size: 14px; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { vertical-align: top; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th { background: #f97316; color: white; padding: 8px 12px; text-align: left; font-size: 11px; }
        table.items td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
        table.items tr:nth-child(even) td { background: #f9fafb; }
        .total td { font-weight: bold; padding-top: 12px; }
        .grand-total td { font-weight: bold; font-size: 14px; color: #ea580c; padding-top: 8px; border-top: 2px solid #f97316; }
        .footer { text-align: center; color: #9ca3af; font-size: 10px; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .status-paid { color: #16a34a; font-weight: bold; }
        .status-unpaid { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <div class="subtitle">{{ $invoice->invoice_number }}</div>
    </div>
    <div class="info">
        <table>
            <tr>
                <td>
                    <strong>Pelanggan:</strong><br>
                    {{ $invoice->customer_name }}<br>
                    @if($invoice->customer_phone){{ $invoice->customer_phone }}<br>@endif
                    @if($invoice->customer_email){{ $invoice->customer_email }}@endif
                </td>
                <td class="text-right">
                    <strong>Tanggal:</strong> {{ $invoice->issue_date->format('d/m/Y') }}<br>
                    <strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d/m/Y') }}<br>
                    <strong>Status:</strong>
                    @if($invoice->status === 'paid')
                        <span class="status-paid">Lunas</span>
                    @elseif($invoice->status === 'overdue')
                        <span class="status-unpaid">Overdue</span>
                    @else
                        <span class="status-unpaid">{{ ucfirst($invoice->status) }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    @if($invoice->notes)
    <p style="margin-bottom: 12px;"><strong>Catatan:</strong> {{ $invoice->notes }}</p>
    @endif
    <table class="items">
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th>Deskripsi</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item['description'] }}</td>
                <td class="text-right">{{ number_format($item['quantity'], 0) }}</td>
                <td class="text-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="4" class="text-right">Subtotal</td>
                <td class="text-right">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($invoice->tax > 0)
            <tr>
                <td colspan="4" class="text-right">Pajak</td>
                <td class="text-right">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td colspan="4" class="text-right">Total</td>
                <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    <div class="footer">
        <p>Dicetak pada {{ now()->format('d/m/Y H:i') }} | MoneyKu</p>
    </div>
</body>
</html>
