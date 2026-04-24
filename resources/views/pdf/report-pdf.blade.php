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

        /* Header table - 3 kolom */
        .header-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            padding: 5px;
            vertical-align: middle;
        }

        /* Kolom logo dan kolom kanan kosong - sama lebarnya */
        .side-column {
            width: 100px;
        }

        /* Kolom tengah untuk info */
        .center-column {
            text-align: center;
        }

        .logo-image {
            width: 80px;
            height: auto;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .address-text {
            font-size: 10px;
            line-height: 1.4;
            margin: 2px 0;
        }

        /* Garis */
        .garis-line {
            border-bottom: 2px solid #000000;
            margin: 10px 0 15px 0;
        }

        /* Judul laporan */
        .report-title-center {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0 8px 0;
        }

        .report-period {
            text-align: center;
            font-size: 11px;
            margin: 5px 0;
            font-weight: normal;
        }

        .print-date {
            text-align: center;
            font-size: 10px;
            margin-bottom: 15px;
        }

        /* Tabel data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            border: 1px solid #000000;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }

        .data-table td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Footer */
        .footer-right {
            text-align: right;
            margin-top: 30px;
            font-size: 11px;
        }

        .footer-copyright {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <!-- ========== HEADER DENGAN TABEL ========== -->
    <!-- ========== HEADER: LOGO KIRI, INFO TENGAH ========== -->
    <table class="header-table">
        <tr>
            <td class="side-column">
                @php
                    $logoPath = public_path('images/Logo LF.png');
                    if (!file_exists($logoPath)) {
                        $logoPath = public_path('images/logo_LF.png');
                    }
                    if (!file_exists($logoPath)) {
                        $logoPath = public_path('images/logo-lf.png');
                    }
                    $logoData = '';
                    if (file_exists($logoPath)) {
                        $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                    }
                @endphp
                @if ($logoData)
                    <img src="{{ $logoData }}" class="logo-image" alt="Logo">
                @else
                    <div style="width:80px;height:80px;border:1px solid #ccc;text-align:center;line-height:80px;">LOGO
                    </div>
                @endif
            </td>
            <td class="center-column">
                <div class="company-name">LUXURY FURNITURE</div>
                <div class="address-text">Jl. Raya Kerobokan No.212, Kerobokan Kelod, Kec. Kuta Utara, Kabupaten Badung,
                    Bali 80361</div>
                <div class="address-text">Telp.: 0361730170 | Email : info@sevanam-enterprise.com</div>
            </td>
            <td class="side-column">
                &nbsp;
            </td>
        </tr>
    </table>

    <!-- ========== GARIS ========== -->
    <div class="garis-line"></div>

    <!-- ========== JUDUL LAPORAN ========== -->
    <div class="report-title-center">{{ $reportTitle }}</div>
    <!-- ========== PERIODE LAPORAN ========== -->
    <div class="report-period">
        @if ($reportType == 'transaction' || $reportType == 'damaged')
            @if ($startDate && $endDate)
                Tanggal: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d
                {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
            @else
                Tanggal : -
            @endif
        @else
            
        @endif
    </div> <br>

    <!-- ========== TABEL DATA ========== -->
    @if ($reportType == 'stock')
        <table class="data-table">
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
                        <td class="text-right">{{ number_format($item['stock'] ?? 0) }}</td>
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
        <table class="data-table">
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
                        <td class="text-right">{{ number_format($item['total_products'] ?? 0) }}</td>
                        <td class="text-right">{{ number_format($item['total_stock'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data kategori</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif($reportType == 'transaction')
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Deskripsi</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['date'] ?? '-' }}</td>
                        <td>{{ $item['product_code'] ?? '-' }}</td>
                        <td>{{ $item['product'] ?? '-' }}</td>
                        <td>{{ $item['type'] ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item['quantity'] ?? 0) }}</td>
                        <td>{{ $item['condition'] ?? '-' }}</td>
                        <td>{{ $item['description'] ?? '-' }}</td>
                        <td>{{ $item['user'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data transaksi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif($reportType == 'damaged')
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Deskripsi</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['date'] ?? '-' }}</td>
                        <td>{{ $item['product_code'] ?? '-' }}</td>
                        <td>{{ $item['product'] ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item['quantity'] ?? 0) }}</td>
                        <td>{{ $item['description'] ?? '-' }}</td>
                        <td>{{ $item['user'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data barang rusak</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <!-- ========== FOOTER ========== -->
    <div class="footer-right">
        {{ Auth::user()->username ?? (Auth::guard()->user()->username ?? 'superadmin') }},
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </div>

    <div class="footer-copyright">
        &copy; {{ date('Y') }} Luxury Furniture - All rights reserved
    </div>

</body>

</html>
