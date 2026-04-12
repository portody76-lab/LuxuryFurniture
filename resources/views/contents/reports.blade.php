@extends('layout.content')

@section('title', 'Reports - Luxury Furniture')

@section('content')
    <div class="bg-white p-6 rounded-2xl mb-6 shadow-md border border-[#e7ddcf]">
        <h2 class="text-2xl font-bold text-gray-800">Reports</h2>
        <p class="text-[#8b7a66] mt-1">Lihat dan export laporan data furniture</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow h-fit">
            <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-[#c9973a]"></i> Report Filter
            </h3>

            <form method="GET" action="{{ route('contents.reports') }}" id="reportForm">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Laporan</label>
                    <select name="report_type" id="report_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:border-[#c9973a] focus:outline-none">
                        <option value="" disabled {{ !$reportType ? 'selected' : '' }}>-- Pilih Tipe Laporan --</option>
                        <option value="transaction" {{ $reportType == 'transaction' ? 'selected' : '' }}>📋 Laporan Transaksi</option>
                        <option value="stock" {{ $reportType == 'stock' ? 'selected' : '' }}>📦 Laporan Stok Produk</option>
                        <option value="category" {{ $reportType == 'category' ? 'selected' : '' }}>📁 Laporan Kategori</option>
                    </select>
                </div>

                <div class="mb-4" id="periodDiv" style="{{ $reportType && $reportType != 'stock' && $reportType != 'category' ? '' : 'display: none;' }}">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Periode</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-gray-500">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm">
                        </div>
                    </div>
                    <button type="button" onclick="clearPeriod()"
                        class="mt-2 text-sm text-[#c9973a] hover:text-[#b07e28] transition flex items-center gap-1">
                        <i class="fas fa-eraser"></i> Clear Period
                    </button>
                </div>

                <button type="submit" id="generateBtn"
                    class="w-full bg-[#c9973a] hover:bg-[#b07e28] text-white font-semibold py-2 rounded-xl transition">
                    <i class="fas fa-chart-line mr-2"></i> Generate Report
                </button>
            </form>
        </div>

        {{-- REPORT CONTENT --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow" id="reportContent">
            <div class="flex justify-between items-center mb-4 print:hidden">
                <h3 class="font-bold text-lg text-gray-800">
                    <i class="fas fa-table text-[#c9973a] mr-2"></i> Data Laporan
                </h3>
                @if(isset($reportType) && $reportType && isset($reportData) && count($reportData) > 0)
                    <form method="GET" action="{{ route('contents.reports.download') }}" target="_blank">
                        <input type="hidden" name="report_type" value="{{ $reportType }}">
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl transition text-sm">
                            <i class="fas fa-file-pdf mr-1"></i> Download PDF
                        </button>
                    </form>
                @endif
            </div>

            <div class="print-header text-center mb-6 hidden print:block">
                <img src="{{ asset('images/Logo LF.png') }}" alt="Logo" class="w-32 mx-auto mb-2">
                <h2 class="text-xl font-bold">LUXURY FURNITURE</h2>
                <p class="text-sm">{{ $reportTitle ?? 'Laporan' }}</p>
                <p class="text-xs text-gray-500">
                    Periode: {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Semua' }} -
                    {{ $endDate ? date('d/m/Y', strtotime($endDate)) : 'Semua' }}
                </p>
                <hr class="my-3">
            </div>

            @if(!$reportType)
                <div class="text-center py-12">
                    <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-chart-pie text-3xl text-gray-400"></i>
                    </div>
                    <h4 class="text-gray-500 font-medium">Pilih Tipe Laporan</h4>
                    <p class="text-gray-400 text-sm mt-1">Silahkan pilih tipe laporan terlebih dahulu</p>
                </div>

            @elseif(isset($reportData) && count($reportData) == 0)
                <div class="text-center py-12">
                    <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-chart-simple text-3xl text-gray-400"></i>
                    </div>
                    <h4 class="text-gray-500 font-medium">Belum Ada Data</h4>
                    <p class="text-gray-400 text-sm mt-1">Tidak ada data untuk periode yang dipilih</p>
                </div>

            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#f3e4c3]">
                                <th class="rounded-l-xl px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">No</th>
                                @if($reportType == 'transaction')
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Produk</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Kategori</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Jenis</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Jumlah</th>
                                    <th class="rounded-r-xl px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Stok Akhir</th>
                                @elseif($reportType == 'stock')
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Kode</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Produk</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Kategori</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Stok</th>
                                    <th class="rounded-r-xl px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Status</th>
                                @elseif($reportType == 'category')
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Kategori</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Total Produk</th>
                                    <th class="rounded-r-xl px-4 py-3 text-left text-sm font-semibold text-[#7a5c1e]">Total Stok</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData as $index => $row)
                                <tr class="border-b border-[#f3e4c3] hover:bg-[#fdf8f0] transition-colors">
                                    <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $index + 1 }}</td>

                                    @if($reportType == 'transaction')
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $row['date'] }}</td>
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $row['product'] }}</td>
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $row['category'] }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs {{ $row['type'] == 'Masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $row['type'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ number_format($row['quantity']) }}</td>
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ number_format($row['stock']) }}</td>

                                    @elseif($reportType == 'stock')
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $row['code'] }}</td>
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $row['product'] }}</td>
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $row['category'] }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs {{ $row['stock'] <= 5 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                {{ number_format($row['stock']) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $row['status'] }}</td>

                                    @elseif($reportType == 'category')
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ $row['category'] }}</td>
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ number_format($row['total_products']) }}</td>
                                        <td class="px-4 py-3 text-sm text-[#3a3020]">{{ number_format($row['total_stock']) }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right text-sm text-gray-500 print:hidden">
                    Total data: {{ count($reportData) }} record
                </div>

                <div class="print-footer text-center text-xs text-gray-500 mt-6 hidden print:block">
                    <hr class="my-3">
                    <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
                    <p>&copy; {{ date('Y') }} Luxury Furniture - All rights reserved</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function printReport() {
        const printContent = document.getElementById('reportContent');
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <html>
            <head>
                <title>Laporan Luxury Furniture</title>
                <style>
                    body { font-family: 'Inter', sans-serif; padding: 30px; margin: 0; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
                    th { background-color: #f3e4c3; font-weight: 600; }
                    .text-center { text-align: center; }
                    .text-xs { font-size: 12px; }
                    .text-sm { font-size: 14px; }
                    .text-xl { font-size: 20px; }
                    .font-bold { font-weight: bold; }
                    .text-gray-500 { color: #6b7280; }
                    .bg-green-100 { background-color: #d1fae5; color: #047857; padding: 4px 8px; border-radius: 9999px; }
                    .bg-red-100 { background-color: #fee2e2; color: #dc2626; padding: 4px 8px; border-radius: 9999px; }
                    hr { border: none; border-top: 1px solid #e5e7eb; margin: 16px 0; }
                </style>
            </head>
            <body>
                ${printContent.cloneNode(true).innerHTML}
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    }
    
    function clearPeriod() {
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
    }
    
    // Toggle period div based on report type
    const reportType = document.getElementById('report_type');
    const periodDiv = document.getElementById('periodDiv');
    
    function togglePeriod() {
        const type = reportType.value;
        if (type === 'stock' || type === 'category') {
            periodDiv.style.display = 'none';
        } else if (type === 'transaction') {
            periodDiv.style.display = 'block';
        } else {
            periodDiv.style.display = 'none';
        }
    }
    
    reportType.addEventListener('change', togglePeriod);
    togglePeriod();
    
    // Auto hide toast
    setTimeout(() => {
        const toast = document.getElementById('toast-box');
        if (toast) setTimeout(() => toast.style.visibility = 'hidden', 3000);
    }, 100);
</script>

<style>
    @media print {
        .sidebar, .bg-white:first-child, form, .print\:hidden {
            display: none !important;
        }
        #reportContent {
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
        }
        .print-header, .print-footer {
            display: block !important;
        }
        body {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
    .print-header, .print-footer {
        display: none;
    }
</style>
@endsection