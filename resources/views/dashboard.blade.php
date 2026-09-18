@extends('layouts.app')

@section('content')
<!-- SaaS Welcome Banner -->
<!-- Dashboard Executive KPI Banner -->
<div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
    <!-- Header Banner dengan Gradient Premium -->
    <div class="card-header border-0 p-4 p-lg-4 text-white position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <br>
                <h4 class="fw-bold mb-0 text-white">Executive Summary</h4>
            </div>
        </div>
    </div>

    <!-- Body / Content KPI Cards -->
    <div class="card-body p-4 bg-light bg-opacity-50">
        <div class="row g-3">
            
            <!-- 1. Total Omset (PAID) -->
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 bg-success" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Total Omset</span>
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid fa-circle-check me-1"></i>Terlunasi
                                </span>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-wallet fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-dark mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($totalOmset, 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">Total invoice berstatus <strong class="text-success">PAID</strong></p>
                    </div>
                </div>
            </div>

            <!-- 2. Total Piutang (SENT / PENDING) -->
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 bg-warning" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Total Piutang</span>
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid fa-clock me-1"></i>Pending
                                </span>
                            </div>
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-dark mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($totalPiutang, 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">Menunggu pembayaran klien</p>
                    </div>
                </div>
            </div>

            <!-- 3. Total Pengeluaran Kas -->
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 bg-danger" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Pengeluaran Kas</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid fa-arrow-down-long me-1"></i>Expense
                                </span>
                            </div>
                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-money-bill-transfer fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-dark mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($totalExpense, 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">Beban & operasional bisnis</p>
                    </div>
                </div>
            </div>

            <!-- 4. Laba Bersih Komersial -->
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 {{ $netProfit >= 0 ? 'bg-primary' : 'bg-danger' }}" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Laba Bersih</span>
                                <span class="badge {{ $netProfit >= 0 ? 'bg-primary-subtle text-primary border-primary' : 'bg-danger-subtle text-danger border-danger' }} border border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid {{ $netProfit >= 0 ? 'fa-chart-line' : 'fa-chart-line-down' }} me-1"></i>Net Profit
                                </span>
                            </div>
                            <div class="{{ $netProfit >= 0 ? 'bg-primary bg-opacity-10 text-primary' : 'bg-danger bg-opacity-10 text-danger' }} rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-scale-balanced fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }} mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($netProfit, 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">Pendapatan dikurangi beban</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Col 1: 5 Dokumen Invoice Terbaru -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-file-invoice me-2 text-primary"></i> Dokumen Invoice & PO Terbaru
                </h6>
                <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-primary px-2.5 font-xs fw-bold">
                    Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No. Dokumen</th>
                                <th>Customer</th>
                                <th class="text-end">Tagihan</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDocuments as $doc)
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-dark font-monospace" style="font-size: 0.775rem;">{{ $doc->document_number }}</strong>
                                </td>
                                <td>{{ $doc->customer?->name }}</td>
                                <td class="text-end fw-semibold">{{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($doc->total_amount, 0, ',', '.') }}</td>
                                <td class="text-center pe-4">
                                    @if($doc->status == 'PAID')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-xs">PAID</span>
                                    @elseif($doc->status == 'SENT')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-xs">SENT</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 font-xs">DRAFT</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted font-xs">Belum ada dokumen transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Col 2: 5 Transaksi Kas Terbaru -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-receipt me-2 text-danger"></i> Transaksi Kas & Jurnal Terbaru
                </h6>
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-danger px-2.5 font-xs fw-bold">
                    Buku Kas <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No. Transaksi</th>
                                <th>Kategori / Akun</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-center pe-4">Fiskal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $trx)
                            <tr>
                                <td class="ps-4">
                                    <span class="font-monospace text-dark fw-bold" style="font-size: 0.775rem;">{{ $trx->transaction_number }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 160px;" title="{{ $trx->category?->name }}">
                                        [{{ $trx->category?->code }}] {{ $trx->category?->name }}
                                    </div>
                                </td>
                                <td class="text-end fw-bold {{ $trx->type == 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $trx->type == 'income' ? '+' : '-' }} {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center pe-4">
                                    @if($trx->type == 'income')
                                        <span class="badge bg-success bg-opacity-10 text-success font-xs">Income</span>
                                    @elseif($trx->category?->is_tax_deductible)
                                        <span class="badge bg-primary bg-opacity-10 text-primary font-xs">Deductible</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-dark font-xs">Koreksi</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted font-xs">Belum ada transaksi kas tercatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection