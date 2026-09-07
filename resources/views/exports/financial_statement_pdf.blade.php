<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - {{ $reportData['formatted_start_date'] }} s/d {{ $reportData['formatted_end_date'] }}</title>
    <style>
        @page {
            margin: 25px 25px;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        body {
            font-size: 10px;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .company-info {
            font-size: 9px;
            color: #475569;
        }
        .report-header {
            text-align: center;
            margin-bottom: 14px;
        }
        .report-header h2 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .report-header p {
            margin: 2px 0 0 0;
            font-size: 9.5px;
            color: #64748b;
        }
        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
            margin: 10px 0 4px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table-data th {
            background-color: #f1f5f9;
            border: 1px solid #94a3b8;
            padding: 5px 6px;
            font-size: 8.5px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .table-data td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 8.5px;
        }
        .summary-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .summary-box td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            width: 25%;
            vertical-align: top;
        }
        .summary-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }
        .summary-value {
            font-size: 11px;
            font-weight: bold;
            margin-top: 2px;
        }
        .text-success { color: #166534; }
        .text-danger { color: #991b1b; }
        .text-primary { color: #1e40af; }
        .text-warning { color: #854d0e; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .total-row {
            font-weight: bold;
            background-color: #f8fafc;
        }
        .signatures {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signatures td {
            width: 33.33%;
            text-align: center;
            font-size: 9px;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <!-- Header Perusahaan -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="company-name">{{ $setting->company_name ?? 'PT DWITAMA CIPTA INTERNUSA' }}</div>
                <div class="company-info">
                    {{ $setting->company_address ?? 'Jakarta, Indonesia' }}<br>
                    NPWP: <strong>{{ $setting->company_npwp ?? '-' }}</strong> | Telepon: {{ $setting->company_phone ?? '-' }}
                </div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <div style="font-size: 11px; font-weight: bold; color: #1e40af;">EXECUTIVE FINANCIAL STATEMENT</div>
                <div style="font-size: 8.5px; color: #64748b;">
                    Tanggal Cetak: {{ date('d/m/Y H:i') }}<br>
                    Status: Resmi (Internal & Audit)
                </div>
            </td>
        </tr>
    </table>

    <!-- Judul Laporan -->
    <div class="report-header">
        <h2>LAPORAN ARUS KAS & PENAGIHAN KEUANGAN</h2>
        <p>Periode: <strong>{{ $reportData['formatted_start_date'] }}</strong> s/d <strong>{{ $reportData['formatted_end_date'] }}</strong> (Mata Uang: IDR)</p>
    </div>

    <!-- 1. Executive Summary Box -->
    <div class="section-title">1. Ringkasan Eksekutif (Executive Summary)</div>
    <table class="summary-box">
        <tr>
            <td style="background-color: #f0fdf4;">
                <div class="summary-label">Total Pemasukan Kas</div>
                <div class="summary-value text-success">Rp {{ number_format($reportData['total_income'], 0, ',', '.') }}</div>
                <div style="font-size: 7.5px; color: #166534; margin-top: 2px;">
                    Paid Inv: Rp {{ number_format($reportData['paid_invoices_income'], 0, ',', '.') }}
                </div>
            </td>
            <td style="background-color: #fef2f2;">
                <div class="summary-label">Total Pengeluaran Kas</div>
                <div class="summary-value text-danger">Rp {{ number_format($reportData['total_expense'], 0, ',', '.') }}</div>
                <div style="font-size: 7.5px; color: #991b1b; margin-top: 2px;">
                    Fiskal: Rp {{ number_format($reportData['deductible_expense'], 0, ',', '.') }}
                </div>
            </td>
            <td style="background-color: #eff6ff;">
                <div class="summary-label">Laba / Rugi Bersih</div>
                <div class="summary-value {{ $reportData['net_profit'] >= 0 ? 'text-primary' : 'text-danger' }}">
                    Rp {{ number_format($reportData['net_profit'], 0, ',', '.') }}
                </div>
                <div style="font-size: 7.5px; color: #1e40af; margin-top: 2px;">
                    Net Cashflow Periode Ini
                </div>
            </td>
            <td style="background-color: #fefce8;">
                <div class="summary-label">Piutang Belum Terbayar</div>
                <div class="summary-value text-warning">Rp {{ number_format($reportData['invoicing_summary']['unpaid_amount'], 0, ',', '.') }}</div>
                <div style="font-size: 7.5px; color: #854d0e; margin-top: 2px;">
                    {{ $reportData['invoicing_summary']['sent_count'] }} Invoice Sent pending
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. Rekap Buka Invoicing -->
    <div class="section-title">2. Rekapitulasi Performa Penerbitan & Penagihan Invoice</div>
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 35%;" class="text-start">Status Dokumen Invoice</th>
                <th style="width: 15%;">Jumlah Dokumen</th>
                <th style="width: 25%;" class="text-end">Total Nilai Tagihan (Rp)</th>
                <th style="width: 25%;">Tingkat Kolektibilitas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-start"><strong>PAID</strong> - Invoice Lunas Terbayar</td>
                <td class="text-center">{{ $reportData['invoicing_summary']['paid_count'] }}</td>
                <td class="text-end text-success"><strong>{{ number_format($reportData['invoicing_summary']['paid_amount'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ $reportData['invoicing_summary']['collection_rate'] }}%</strong> (Tertagih)</td>
            </tr>
            <tr>
                <td class="text-start"><strong>SENT</strong> - Invoice Terkirim (Piutang Menunggu Bayar)</td>
                <td class="text-center">{{ $reportData['invoicing_summary']['sent_count'] }}</td>
                <td class="text-end text-primary"><strong>{{ number_format($reportData['invoicing_summary']['sent_amount'], 0, ',', '.') }}</strong></td>
                <td class="text-center">Piutang Berjalan</td>
            </tr>
            <tr>
                <td class="text-start"><strong>DRAFT</strong> - Konsep Invoice (Belum Terbit)</td>
                <td class="text-center">{{ $reportData['invoicing_summary']['draft_count'] }}</td>
                <td class="text-end text-muted">{{ number_format($reportData['invoicing_summary']['draft_amount'], 0, ',', '.') }}</td>
                <td class="text-center">Konsep Internal</td>
            </tr>
            <tr class="total-row">
                <td class="text-start">TOTAL PENERBITAN INVOICE PERIODE INI</td>
                <td class="text-center">{{ $reportData['invoicing_summary']['total_count'] }}</td>
                <td class="text-end">{{ number_format($reportData['invoicing_summary']['total_amount'], 0, ',', '.') }}</td>
                <td class="text-center">100%</td>
            </tr>
        </tbody>
    </table>

    <!-- 3. Rincian Arus Transaksi (Transaction Ledger) -->
    <div class="section-title">3. Rincian Buku Kas & Mutasi Transaksi (Transaction Ledger)</div>
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 10%;">TANGGAL</th>
                <th style="width: 15%;">NO. REF</th>
                <th style="width: 20%;" class="text-start">AKUN / KATEGORI</th>
                <th style="width: 23%;" class="text-start">URAIAN / RELASI</th>
                <th style="width: 10%;">METODE</th>
                <th style="width: 11%;" class="text-end">MASUK (RP)</th>
                <th style="width: 11%;" class="text-end">KELUAR (RP)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['ledger_entries'] as $item)
            <tr>
                <td class="text-center">{{ $item['formatted_date'] }}</td>
                <td class="text-center font-mono" style="font-size: 7.5px;">{{ $item['reference_number'] }}</td>
                <td class="text-start">[{{ $item['category_code'] }}] {{ $item['category_name'] }}</td>
                <td class="text-start">
                    {{ $item['description'] }}
                    @if($item['party_name'] && $item['party_name'] !== '-')
                        <span style="font-size: 7.5px; color: #64748b; display: block;">Relasi: {{ $item['party_name'] }}</span>
                    @endif
                </td>
                <td class="text-center" style="font-size: 7.5px;">{{ $item['payment_method'] }}</td>
                <td class="text-end text-success">
                    {{ $item['amount_in'] > 0 ? number_format($item['amount_in'], 0, ',', '.') : '-' }}
                </td>
                <td class="text-end text-danger">
                    {{ $item['amount_out'] > 0 ? number_format($item['amount_out'], 0, ',', '.') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-3 text-muted">Tidak ada transaksi tercatat pada rentang tanggal ini.</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-end">TOTAL MUTASI ARUS KAS:</td>
                <td class="text-end text-success">Rp {{ number_format($reportData['total_income'], 0, ',', '.') }}</td>
                <td class="text-end text-danger">Rp {{ number_format($reportData['total_expense'], 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #0f172a; color: #ffffff; font-weight: bold;">
                <td colspan="5" class="text-end">SALDO SURPLUS / DEFISIT BERSIH (NET CASHFLOW):</td>
                <td colspan="2" class="text-end" style="color: #ffffff; font-size: 10px;">
                    Rp {{ number_format($reportData['net_profit'], 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Blok Tanda Tangan & Otorisasi -->
    <table class="signatures">
        <tr>
            <td>
                Dibuat Oleh,<br>
                <span style="color: #64748b;">Staf Administrasi Keuangan</span>
                <br><br><br><br>
                ( .................................................. )
            </td>
            <td>
                Diperiksa Oleh,<br>
                <span style="color: #64748b;">Finance & Accounting Manager</span>
                <br><br><br><br>
                ( .................................................. )
            </td>
            <td>
                Disetujui Oleh,<br>
                <span style="color: #64748b;">Direktur Utama / Pimpinan</span>
                <br><br><br><br>
                ( <strong>{{ $setting->company_director ?? $setting->company_name ?? 'Direktur' }}</strong> )
            </td>
        </tr>
    </table>

</body>
</html>
