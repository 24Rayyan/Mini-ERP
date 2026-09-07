<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lampiran Khusus: Daftar Nominatif Biaya Entertainment & Promosi - {{ $year }}</title>
    <style>
        @page {
            margin: 20px 25px;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        body {
            font-size: 10px;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .company-name {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .company-info {
            font-size: 9px;
            color: #475569;
        }
        .lampiran-box {
            border: 1px solid #0f172a;
            padding: 4px 8px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            background-color: #f8fafc;
        }
        .report-title {
            text-align: center;
            margin-bottom: 12px;
        }
        .report-title h2 {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .report-title p {
            margin: 2px 0 0 0;
            font-size: 9.5px;
            color: #64748b;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .content-table th {
            background-color: #f1f5f9;
            border: 1px solid #64748b;
            padding: 5px 4px;
            font-size: 8.5px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .content-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 8.5px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f1f5f9;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .signatures {
            width: 100%;
            margin-top: 20px;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            font-size: 9px;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen DJP -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div class="company-name">{{ $setting->company_name ?? 'PT DWITAMA CIPTA INTERNUSA' }}</div>
                <div class="company-info">
                    NPWP: <strong>{{ $setting->company_npwp ?? '-' }}</strong><br>
                    Alamat: {{ $setting->company_address ?? 'Jakarta, Indonesia' }}
                </div>
            </td>
            <td style="width: 45%; text-align: right; vertical-align: top;">
                <div class="lampiran-box">
                    LAMPIRAN KHUSUS SPT TAHUNAN PPh BADAN<br>
                    DAFTAR NOMINATIF BIAYA ENTERTAINMENT DAN SEJENISNYA<br>
                    <span style="font-size: 8px; font-weight: normal; color: #475569;">(Berdasarkan Peraturan Dirjen Pajak No. PMK-02/PMK.03/2010)</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Judul Laporan -->
    <div class="report-title">
        <h2>DAFTAR NOMINATIF BIAYA ENTERTAINMENT, JAMUAN & REPRESENTASI</h2>
        <p>Tahun Pajak: <strong>{{ $year }}</strong> {{ $month ? '| Bulan: ' . \Carbon\Carbon::create()->month($month)->translatedFormat('F') : '' }} (Mata Uang: Rupiah)</p>
    </div>

    <!-- Tabel Nominatif DJP -->
    <table class="content-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">NO</th>
                <th colspan="2" style="width: 27%;">PEMBERIAN ENTERTAINMENT</th>
                <th colspan="4" style="width: 45%;">RELASI USAHA / PIHAK KETIGA YANG DIBERIKAN</th>
                <th rowspan="2" style="width: 15%;">JUMLAH (RP)</th>
                <th rowspan="2" style="width: 10%;">NO. BUKTI / TRX</th>
            </tr>
            <tr>
                <th style="width: 9%;">TANGGAL</th>
                <th style="width: 18%;">TEMPAT & LOKASI</th>
                <th style="width: 13%;">NAMA</th>
                <th style="width: 12%;">PERUSAHAAN</th>
                <th style="width: 10%;">JABATAN</th>
                <th style="width: 10%;">JENIS / TUJUAN</th>
            </tr>
            <tr style="background-color: #f8fafc; font-size: 7.5px;">
                <th>(1)</th>
                <th>(2)</th>
                <th>(3)</th>
                <th>(4)</th>
                <th>(5)</th>
                <th>(6)</th>
                <th>(7)</th>
                <th>(8)</th>
                <th>(9)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nominativeList as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->event_date ? $item->event_date->format('d/m/Y') : '-' }}</td>
                <td>{{ $item->location }}</td>
                <td><strong>{{ $item->attendee_name }}</strong></td>
                <td>{{ $item->attendee_company }}</td>
                <td>{{ $item->attendee_position }}</td>
                <td>{{ $item->purpose }}</td>
                <td class="text-end"><strong>{{ number_format($item->transaction?->amount ?? 0, 0, ',', '.') }}</strong></td>
                <td class="text-center" style="font-family: monospace; font-size: 8px;">{{ $item->transaction?->transaction_number }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-4 text-muted">
                    Tidak ada transaksi biaya entertainment dengan daftar nominatif pada periode ini.
                </td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="7" class="text-end" style="padding-right: 10px;">JUMLAH TOTAL PENGELUARAN BIAYA ENTERTAINMENT:</td>
                <td class="text-end"><strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Keterangan & Tanda Tangan -->
    <table class="signatures">
        <tr>
            <td style="text-align: left; vertical-align: top; font-size: 8px; color: #475569;">
                <strong>Catatan Penting:</strong><br>
                1. Daftar nominatif ini dilampirkan pada SPT Tahunan PPh Badan sebagai syarat pembebanan biaya secara fiskal.<br>
                2. Seluruh bukti pengeluaran / nota asli disimpan oleh Wajib Pajak sesuai ketentuan perundang-undangan perpajakan.
            </td>
            <td style="text-align: center; vertical-align: top;">
                {{ $setting->company_city ?? 'Jakarta' }}, {{ date('d F Y') }}<br>
                <strong>Pimpinan / Pengurus Wajib Pajak</strong>
                <br><br><br><br>
                ( <strong>{{ $setting->company_director ?? $setting->company_name ?? 'Direktur' }}</strong> )
            </td>
        </tr>
    </table>

</body>
</html>
