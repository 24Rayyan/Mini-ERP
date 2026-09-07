<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lampiran Nominatif Entertainment SPT</title>
</head>
<body>
    <table border="1">
        <tr>
            <th colspan="9" style="font-size: 14pt; font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff;">
                {{ strtoupper($setting->company_name ?? 'PT DWITAMA CIPTA INTERNUSA') }}
            </th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 11pt; text-align: center;">
                LAMPIRAN KHUSUS SPT TAHUNAN: DAFTAR NOMINATIF BIAYA ENTERTAINMENT & JAMUAN
            </th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 9pt; text-align: center;">
                Tahun Pajak: {{ $year }} {{ $month ? '| Bulan: ' . \Carbon\Carbon::create()->month($month)->translatedFormat('F') : '' }} | NPWP: {{ $setting->company_npwp ?? '-' }}
            </th>
        </tr>
        <tr>
            <td colspan="9"></td>
        </tr>
        <tr style="background-color: #cbd5e1; font-weight: bold; text-align: center;">
            <th>NO</th>
            <th>TANGGAL</th>
            <th>TEMPAT / LOKASI</th>
            <th>NAMA PIHAK KETIGA</th>
            <th>PERUSAHAAN</th>
            <th>JABATAN</th>
            <th>BENTUK & TUJUAN JAMUAN</th>
            <th>JUMLAH (RP)</th>
            <th>NO. TRANSAKSI</th>
        </tr>
        @forelse($nominativeList as $index => $item)
        <tr>
            <td style="text-align: center;">{{ $index + 1 }}</td>
            <td style="text-align: center;">{{ $item->event_date ? $item->event_date->format('d/m/Y') : '-' }}</td>
            <td>{{ $item->location }}</td>
            <td><strong>{{ $item->attendee_name }}</strong></td>
            <td>{{ $item->attendee_company }}</td>
            <td>{{ $item->attendee_position }}</td>
            <td>{{ $item->purpose }}</td>
            <td style="text-align: right;">{{ $item->transaction?->amount ?? 0 }}</td>
            <td style="text-align: center;">{{ $item->transaction?->transaction_number }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align: center;">Tidak ada data kegiatan entertainment pada periode ini.</td>
        </tr>
        @endforelse
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="7" style="text-align: right;">JUMLAH TOTAL PENGELUARAN:</td>
            <td style="text-align: right;">{{ $totalAmount }}</td>
            <td></td>
        </tr>
    </table>
</body>
</html>
