@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-champagne-glasses text-warning me-2"></i> Lampiran Khusus: Daftar Nominatif Biaya Entertainment
        </h3>
        <p class="text-muted mb-0 font-sm">
            Format baku daftar nominatif biaya jamuan tamu / relasi bisnis sesuai Peraturan Direktur Jenderal Pajak (PMK No. 02/PMK.03/2010).
        </p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <div class="btn-group shadow-sm">
            <a href="{{ route('reports.entertainment_nominative.pdf', ['year' => $year, 'month' => $month]) }}" class="btn btn-danger" target="_blank">
                <i class="fa-solid fa-file-pdf me-1.5"></i> Cetak Lampiran DJP (PDF)
            </a>
            <a href="{{ route('reports.entertainment_nominative.excel', ['year' => $year, 'month' => $month]) }}" class="btn btn-success">
                <i class="fa-solid fa-file-excel me-1.5"></i> Export Excel
            </a>
        </div>
    </div>
</div>

<!-- Filter Periode -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.entertainment_nominative') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Tahun Pajak</label>
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Bulan</label>
                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="" {{ empty($month) ? 'selected' : '' }}>Sepanjang Tahun (Tahunan)</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6 text-end">
                <span class="badge bg-light text-dark border p-2 font-xs">
                    <i class="fa-solid fa-building me-1 text-primary"></i> {{ $setting->company_name ?? 'PT Dwitama Cipta Internusa' }} | NPWP: {{ $setting->company_npwp ?? '-' }}
                </span>
            </div>
        </form>
    </div>
</div>

<!-- Info Alert -->
<div class="alert alert-warning border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert">
    <i class="fa-solid fa-triangle-exclamation fs-4 me-3 text-warning"></i>
    <div class="font-sm">
        <strong>Ketentuan Perpajakan DJP:</strong> Pengeluaran untuk jamuan, representasi, dan entertainment hanya dapat dikurangkan dari penghasilan bruto (Deductible) jika Wajib Pajak membuat dan melampirkan <strong>Daftar Nominatif</strong> ini pada SPT Tahunan PPh Badan.
    </div>
</div>

<!-- Nominative Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-table-list me-2 text-primary"></i> Daftar Nominatif Biaya Promosi / Entertainment & Jamuan
            </h5>
            <span class="badge bg-dark text-white px-3 py-1.5 font-sm">
                Total: Rp {{ number_format($totalAmount, 0, ',', '.') }} ({{ $nominativeList->count() }} Kegiatan)
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 font-sm">
                <thead class="bg-light text-center text-uppercase font-xs">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th style="width: 100px;">Tanggal</th>
                        <th style="width: 180px;">Tempat / Alamat</th>
                        <th>Nama Pihak Ketiga</th>
                        <th>Perusahaan / Instansi</th>
                        <th style="width: 140px;">Jabatan</th>
                        <th>Tujuan & Bentuk Jamuan</th>
                        <th style="width: 140px;">Jumlah (Rp)</th>
                        <th style="width: 70px;">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nominativeList as $index => $item)
                    <tr>
                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                        <td class="text-center">
                            {{ $item->event_date ? $item->event_date->format('d/m/Y') : '-' }}
                        </td>
                        <td>{{ $item->location }}</td>
                        <td class="fw-bold text-dark">{{ $item->attendee_name }}</td>
                        <td>{{ $item->attendee_company }}</td>
                        <td>{{ $item->attendee_position }}</td>
                        <td>{{ $item->purpose }}</td>
                        <td class="text-end fw-bold text-dark">
                            {{ number_format($item->transaction?->amount ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($item->transaction?->receipt_file_path)
                                <a href="{{ route('transactions.receipt', $item->transaction->id) }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-1 font-xs" title="Lihat Bukti Nota">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-champagne-glasses fs-2 mb-2 d-block text-secondary"></i>
                            Belum ada catatan biaya entertainment dengan daftar nominatif untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($nominativeList->isNotEmpty())
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td colspan="7" class="text-end pe-3 text-uppercase">Jumlah Pengeluaran Biaya Entertainment:</td>
                        <td class="text-end text-primary fs-6">
                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
