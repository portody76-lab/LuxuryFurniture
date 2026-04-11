<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }} - Luxury Furniture</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #c9973a;
            padding-bottom: 10px;
        }

        h2 {
            color: #5a4a1e;
            margin: 0;
            font-size: 18px;
        }

        .date {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #f3e4c3;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .text-center {
            text-align: center;
        }

        .badge-low {
            color: #dc2626;
            font-weight: bold;
        }

        .badge-in {
            color: #16a34a;
            font-weight: bold;
        }

        .badge-out {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LUXURY FURNITURE</h2>
        <div class="date">{{ $reportTitle }}</div>
        <div class="date">Dicetak: {{ date('d/m/Y H:i:s') }}</div>
        @if($startDate && $endDate)
            <div class="date">Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}
            </div>
        @endif
    </div>

    @if($reportType == 'stock')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['code'] ?? '-' }}</td>
                        <td>{{ $item['product'] ?? '-' }}</td>
                        <td>{{ $item['category'] ?? '-' }}</td>
                        <td class="{{ ($item['stock'] ?? 0) <= 5 ? 'badge-low' : '' }}">{{ number_format($item['stock'] ?? 0) }}
                        </td>
                        <td>{{ $item['status'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data produk</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType == 'category')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Total Produk</th>
                    <th>Total Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['category'] ?? '-' }}</td>
                        <td>{{ number_format($item['total_products'] ?? 0) }}</td>
                        <td>{{ number_format($item['total_stock'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data kategori</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType == 'transaction')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['date'] ?? '-' }}</td>
                        <td>{{ $item['product'] ?? '-' }}</td>
                        <td>{{ $item['category'] ?? '-' }}</td>
                        <td class="{{ $item['type'] == 'Masuk' ? 'badge-in' : 'badge-out' }}">{{ $item['type'] ?? '-' }}</td>
                        <td>{{ number_format($item['quantity'] ?? 0) }}</td>
                        <td>{{ number_format($item['stock'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data transaksi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>&copy; {{ date('Y') }} Luxury Furniture - All rights reserved</p>
    </div>
</body>

</html>