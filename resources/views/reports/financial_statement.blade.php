@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h3 class="fw-bold text-dark mb-1">
         Laporan Keuangan Profesional
        </h3>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <div class="btn-group shadow-sm">
            <a href="{{ route('reports.financial_statement.pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'category_id' => $categoryId, 'type' => $type]) }}" class="btn btn-danger" target="_blank">
                <i class="fa-solid fa-file-pdf me-1.5"></i> Download PDF
            </a>
            <a href="{{ route('reports.financial_statement.excel', ['start_date' => $startDate, 'end_date' => $endDate, 'category_id' => $categoryId, 'type' => $type]) }}" class="btn btn-success">
                <i class="fa-solid fa-file-excel me-1.5"></i> Export Excel
            </a>
        </div>
    </div>
</div>

<!-- Filter Bar Komprehensif -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.financial_statement') }}" id="filterForm" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Kategori Akun (COA)</label>
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            [{{ $cat->code }}] {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Tipe Transaksi</label>
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua (Income & Expense)</option>
                    <option value="income" {{ $type == 'income' ? 'selected' : '' }}>Pemasukan (Income)</option>
                    <option value="expense" {{ $type == 'expense' ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
            </div>

            <!-- Quick Presets -->
            <div class="col-12 mt-2 pt-2 border-top d-flex gap-2 align-items-center flex-wrap">
                <span class="font-xs text-muted fw-bold text-uppercase me-1">Pilihan Cepat:</span>
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0.5 px-2 font-xs" onclick="setPreset('this_month')">Bulan Ini</button>
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0.5 px-2 font-xs" onclick="setPreset('last_month')">Bulan Lalu</button>
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0.5 px-2 font-xs" onclick="setPreset('this_year')">Tahun Berjalan</button>
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0.5 px-2 font-xs" onclick="setPreset('last_30_days')">30 Hari Terakhir</button>
            </div>
        </form>
    </div>
</div>

<!-- Header Info Periode -->
<div class="alert alert-primary bg-primary bg-opacity-10 border-0 shadow-sm rounded-3 py-2 px-3 mb-4 d-flex justify-content-between align-items-center">
    <div class="font-sm text-primary-emphasis">
        <i class="fa-solid fa-calendar-days me-1.5"></i> Rentang Laporan: <strong>{{ $reportData['formatted_start_date'] }}</strong> s/d <strong>{{ $reportData['formatted_end_date'] }}</strong>
    </div>
    <span class="badge bg-dark font-xs">
        {{ $setting->company_name ?? 'PT Dwitama Cipta Internusa' }}
    </span>
</div>

<!-- 1. Executive Summary Cards -->
<!-- Executive Financial Summary Cards Container -->
<div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
    <!-- Header Banner dengan Gradient Premium -->
    <div class="card-header border-0 p-4 p-lg-4 text-white position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold mb-0 text-white">Executive Financial Summary</h4>
            </div>
        </div>
    </div>

    <!-- Body / Content KPI Cards -->
    <div class="card-body p-4 bg-light bg-opacity-50">
        <div class="row g-3">
            
            <!-- 1. Total Pemasukan Kas -->
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 bg-success" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Total Pemasukan</span>
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid fa-circle-check me-1"></i>Kas Masuk
                                </span>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-wallet fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-dark mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($reportData['total_income'], 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">
                            Invoice: {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($reportData['paid_invoices_income'], 0, ',', '.') }} | Lainnya: {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($reportData['other_income'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. Total Pengeluaran Kas -->
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 bg-danger" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Total Pengeluaran</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid fa-arrow-down-long me-1"></i>Beban Ops
                                </span>
                            </div>
                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-money-bill-transfer fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-dark mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($reportData['total_expense'], 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">
                            Fiskal: {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($reportData['deductible_expense'], 0, ',', '.') }} | Non: {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($reportData['non_deductible_expense'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- 3. Laba / Rugi Bersih -->
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 {{ $reportData['net_profit'] >= 0 ? 'bg-primary' : 'bg-danger' }}" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Laba / Rugi Bersih</span>
                                <span class="badge {{ $reportData['net_profit'] >= 0 ? 'bg-primary-subtle text-primary border-primary' : 'bg-danger-subtle text-danger border-danger' }} border border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid {{ $reportData['net_profit'] >= 0 ? 'fa-chart-line' : 'fa-chart-line-down' }} me-1"></i>Net Profit
                                </span>
                            </div>
                            <div class="{{ $reportData['net_profit'] >= 0 ? 'bg-primary bg-opacity-10 text-primary' : 'bg-danger bg-opacity-10 text-danger' }} rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-scale-balanced fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder {{ $reportData['net_profit'] >= 0 ? 'text-primary' : 'text-danger' }} mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($reportData['net_profit'], 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">Pemasukan Bersih - Pengeluaran</p>
                    </div>
                </div>
            </div>

            <!-- 4. Piutang Belum Terbayar -->
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 bg-warning" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Piutang Aktif</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid fa-clock me-1"></i>Pending
                                </span>
                            </div>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-dark mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($reportData['invoicing_summary']['unpaid_amount'], 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">{{ $reportData['invoicing_summary']['sent_count'] }} Invoice Sent pending</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 2. Rekap Performa Invoicing Periode Ini -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Rekap Penagihan & Performa Invoice Periode Ini
            </h6>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-xs">
                Collection Rate: {{ $reportData['invoicing_summary']['collection_rate'] }}%
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 font-sm">
                <thead class="bg-light text-center">
                    <tr>
                        <th class="text-start ps-4">Status Tagihan</th>
                        <th style="width: 15%;">Jumlah Dokumen</th>
                        <th style="width: 25%;">Total Nominal Tagihan</th>
                        <th style="width: 30%;">Persentase Kolektibilitas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-success-subtle text-success border border-success-subtle me-1.5">PAID</span>
                            Invoice Lunas (Terbayar)
                        </td>
                        <td class="text-center fw-bold">{{ $reportData['invoicing_summary']['paid_count'] }}</td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($reportData['invoicing_summary']['paid_amount'], 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $reportData['invoicing_summary']['collection_rate'] }}%;"></div>
                                </div>
                                <span class="font-xs fw-bold">{{ $reportData['invoicing_summary']['collection_rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1.5">SENT</span>
                            Invoice Terkirim (Piutang Menunggu Bayar)
                        </td>
                        <td class="text-center fw-bold">{{ $reportData['invoicing_summary']['sent_count'] }}</td>
                        <td class="text-end fw-bold text-primary">Rp {{ number_format($reportData['invoicing_summary']['sent_amount'], 0, ',', '.') }}</td>
                        <td>
                            <span class="text-muted font-xs">Piutang Lancar</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle me-1.5">DRAFT</span>
                            Invoice Konsep (Draft)
                        </td>
                        <td class="text-center fw-bold">{{ $reportData['invoicing_summary']['draft_count'] }}</td>
                        <td class="text-end text-muted">Rp {{ number_format($reportData['invoicing_summary']['draft_amount'], 0, ',', '.') }}</td>
                        <td><span class="text-muted font-xs">Belum Diterbitkan</span></td>
                    </tr>
                    <tr class="fw-bold bg-light">
                        <td class="ps-4 text-uppercase">Total Penerbitan Invoice</td>
                        <td class="text-center">{{ $reportData['invoicing_summary']['total_count'] }}</td>
                        <td class="text-end text-dark">Rp {{ number_format($reportData['invoicing_summary']['total_amount'], 0, ',', '.') }}</td>
                        <td>100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 3. Rincian Arus Transaksi (Transaction Ledger) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> Rincian Buku Kas Kronologis (Transaction Ledger)
                </h6>
                <small class="text-muted">{{ count($reportData['ledger_entries']) }} entri transaksi tercatat</small>
            </div>
            <span class="badge bg-dark text-white px-3 py-1 font-xs font-mono">
                Saldo Akhir: Rp {{ number_format($reportData['net_profit'], 0, ',', '.') }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 font-sm">
                <thead class="bg-light text-uppercase font-xs">
                    <tr>
                        <th class="ps-4" style="width: 100px;">Tanggal</th>
                        <th style="width: 140px;">No. Referensi</th>
                        <th style="width: 180px;">Kategori / Akun</th>
                        <th>Keterangan / Relasi</th>
                        <th style="width: 110px;">Metode</th>
                        <th class="text-end text-success" style="width: 130px;">Masuk (Rp)</th>
                        <th class="text-end text-danger" style="width: 130px;">Keluar (Rp)</th>
                        <th class="text-end pe-4" style="width: 140px;">Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['ledger_entries'] as $item)
                    <tr>
                        <td class="ps-4 font-mono font-xs">{{ $item['formatted_date'] }}</td>
                        <td>
                            <span class="font-mono text-dark fw-bold" style="font-size: 0.775rem;">{{ $item['reference_number'] }}</span>
                        </td>
                        <td>
                            <span class="font-mono font-xs text-muted">[{{ $item['category_code'] }}]</span>
                            <span class="fw-semibold text-dark">{{ $item['category_name'] }}</span>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 250px;" title="{{ $item['description'] }}">
                                {{ $item['description'] }}
                            </div>
                            @if($item['party_name'] && $item['party_name'] !== '-')
                                <small class="text-muted font-xs d-block">
                                    <i class="fa-solid fa-user me-1"></i> {{ $item['party_name'] }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-xs">{{ $item['payment_method'] }}</span>
                        </td>
                        <td class="text-end fw-bold text-success">
                            {{ $item['amount_in'] > 0 ? number_format($item['amount_in'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end fw-bold text-danger">
                            {{ $item['amount_out'] > 0 ? number_format($item['amount_out'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end pe-4 fw-bold {{ $item['running_balance'] >= 0 ? 'text-dark' : 'text-danger' }}">
                            {{ number_format($item['running_balance'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fs-2 mb-2 d-block text-secondary"></i>
                            Tidak ada transaksi yang cocok dengan filter tanggal/kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($reportData['ledger_entries']) > 0)
                <tfoot class="bg-light fw-bold font-sm">
                    <tr>
                        <td colspan="5" class="text-end pe-3 text-uppercase">Total Mutasi Arus Kas:</td>
                        <td class="text-end text-success">Rp {{ number_format($reportData['total_income'], 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($reportData['total_expense'], 0, ',', '.') }}</td>
                        <td class="text-end pe-4 text-primary">Rp {{ number_format($reportData['net_profit'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- 4. Breakdown Kategori Perkiraan -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h6 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-tags text-primary me-2"></i> Rekapitulasi per Akun Kategori (Chart of Accounts)
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 font-sm">
                <thead class="bg-light font-xs text-uppercase">
                    <tr>
                        <th class="ps-4" style="width: 120px;">Kode</th>
                        <th>Nama Akun</th>
                        <th style="width: 140px;">Tipe</th>
                        <th style="width: 220px;">Status Pajak Fiskal</th>
                        <th class="text-center" style="width: 100px;">Jumlah Trx</th>
                        <th class="text-end pe-4" style="width: 180px;">Total Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['category_breakdown'] as $catItem)
                    <tr>
                        <td class="ps-4 font-mono font-xs fw-bold text-dark">{{ $catItem['code'] }}</td>
                        <td class="fw-bold text-dark">{{ $catItem['name'] }}</td>
                        <td>
                            @if($catItem['type'] == 'income')
                                <span class="badge bg-success-subtle text-success font-xs">Income</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger font-xs">Expense</span>
                            @endif
                        </td>
                        <td>
                            @if($catItem['type'] == 'income')
                                <span class="text-muted font-xs">Penghasilan Bruto</span>
                            @elseif($catItem['is_tax_deductible'])
                                <span class="badge bg-primary-subtle text-primary font-xs">Deductible (Biaya Fiskal)</span>
                            @else
                                <span class="badge bg-warning-subtle text-dark font-xs">Koreksi Positif (Non-Deductible)</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $catItem['count'] }}</td>
                        <td class="text-end pe-4 fw-bold {{ $catItem['type'] == 'income' ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($catItem['total_amount'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setPreset(preset) {
        var startInput = document.getElementById('start_date');
        var endInput = document.getElementById('end_date');
        var now = new Date();

        if (preset === 'this_month') {
            var firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            var lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(lastDay);
        } else if (preset === 'last_month') {
            var firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            var lastDay = new Date(now.getFullYear(), now.getMonth(), 0);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(lastDay);
        } else if (preset === 'this_year') {
            var firstDay = new Date(now.getFullYear(), 0, 1);
            var lastDay = new Date(now.getFullYear(), 11, 31);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(lastDay);
        } else if (preset === 'last_30_days') {
            var pastDate = new Date();
            pastDate.setDate(now.getDate() - 30);
            startInput.value = formatDate(pastDate);
            endInput.value = formatDate(now);
        }

        document.getElementById('filterForm').submit();
    }

    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }
</script>
@endpush
