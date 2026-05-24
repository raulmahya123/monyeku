<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
        p { text-align: center; font-size: 11px; color: #666; margin-top: 0; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f97316; color: #fff; padding: 8px 6px; text-align: left; font-size: 10px; text-transform: uppercase; }
        td { padding: 6px; border-bottom: 1px solid #eee; font-size: 11px; }
        td.right, th.right { text-align: right; }
        .total { font-weight: bold; background: #fff7ed; }
    </style>
</head>
<body>
    <h1>Laporan Stok Produk</h1>
    <p>Per {{ now()->format('d/m/Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th class="right">Stok</th>
                <th class="right">Harga Beli</th>
                <th class="right">Nilai Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->code }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category?->name ?? '-' }}</td>
                <td>{{ $p->unit }}</td>
                <td class="right">{{ number_format($p->stock, 0) }}</td>
                <td class="right">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($p->stock * $p->purchase_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="6" class="right">Total Nilai Stok</td>
                <td class="right">Rp {{ number_format($products->sum(fn($p) => $p->stock * $p->purchase_price), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
