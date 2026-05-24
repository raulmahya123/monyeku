<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { color: #f97316; font-size: 20px; margin-bottom: 5px; }
        h2 { font-size: 14px; color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #fff7ed; color: #9a3412; padding: 8px 6px; text-align: left; font-size: 11px; }
        td { padding: 6px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .summary { margin-top: 15px; }
        .summary-item { display: inline-block; margin-right: 30px; }
        .summary-label { font-size: 11px; color: #888; }
        .summary-value { font-size: 16px; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 10px; color: #aaa; text-align: center; }
        .header { border-bottom: 2px solid #f97316; padding-bottom: 10px; margin-bottom: 15px; }
        .badge-income { color: #16a34a; font-weight: bold; }
        .badge-expense { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MoneyKu</h1>
        <h2>Laporan Arus Kas</h2>
        <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Pemasukan</div>
            <div class="summary-value text-green">Rp {{ number_format($income, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Pengeluaran</div>
            <div class="summary-value text-red">Rp {{ number_format($expense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Saldo Bersih</div>
            <div class="summary-value" style="color: {{ $net >= 0 ? '#f97316' : '#dc2626' }}">Rp {{ number_format($net, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Metode</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->transaction_date->format('d/m/Y') }}</td>
                <td><span class="{{ $t->type === 'income' ? 'badge-income' : 'badge-expense' }}">{{ $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span></td>
                <td>{{ $t->category?->name ?? '-' }}</td>
                <td>{{ $t->description ?? '-' }}</td>
                <td>{{ $t->payment_method === 'cash' ? 'Kas' : 'Bank' }}</td>
                <td class="text-right {{ $t->type === 'income' ? 'text-green' : 'text-red' }}">
                    Rp {{ number_format($t->amount, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i') }} &mdash; MoneyKu v1.0
    </div>
</body>
</html>
