@extends('layouts.app')

@section('content')
<!-- SaaS Welcome Banner -->
<div class="card border-0 text-white shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0b1329 0%, #1e293b 100%);">
    <div class="card-body p-4 p-lg-5 position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-primary bg-opacity-25 text-white border border-primary rounded-pill px-3 py-1 me-2 fw-bold" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-chart-line me-1"></i> Mini ERP Financial Management
                    </span>
                    <small class="text-white-50">Komersial & Fiskal Terpadu</small>
                </div>
                <h2 class="fw-bolder text-white mb-2 tracking-tight">Financial Control Hub</h2>
                <p class="text-white-50 mb-0">Pantau omset invoice, piutang berjalan, pengeluaran kas, serta proyeksi laba bersih <strong>{{ $setting->company_name ?? 'Perusahaan' }}</strong> secara real-time.</p>
            </div>
            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                    <a href="{{ route('documents.create') }}" class="btn btn-primary px-3 py-2 shadow-sm fw-bold">
                        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Buat Invoice
                    </a>
                    <a href="{{ route('transactions.create', ['type' => 'expense']) }}" class="btn btn-danger px-3 py-2 shadow-sm fw-bold">
                        <i class="fa-solid fa-receipt me-1"></i> Catat Biaya
                    </a>
                    <a href="{{ route('reports.profit_loss') }}" class="btn btn-outline-light px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-scale-balanced me-1"></i> Laba Rugi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4 Key Financial Metrics (Mini ERP KPI Cards) -->
<div class="row g-3 mb-4">
    <!-- Total Omset -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-2 border-start border-4 border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase tracking-wider">Total Omset (PAID)</span>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                        <i class="fa-solid fa-wallet fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bolder text-success mb-1">{{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($totalOmset, 0, ',', '.') }}</h4>
                <div class="d-flex align-items-center text-muted font-xs">
                    <span class="badge bg-success text-white rounded-pill px-2 py-0.5 me-1 fw-bold">Paid</span>
                    Invoice terlunasi
                </div>
            </div>
        </div>
    </div>

    <!-- Total Piutang -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-2 border-start border-4 border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase tracking-wider">Total Piutang (SENT)</span>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                        <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bolder text-warning mb-1">{{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($totalPiutang, 0, ',', '.') }}</h4>
                <div class="d-flex align-items-center text-muted font-xs">
                    <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5 me-1 fw-bold">Pending</span>
                    Menunggu pembayaran
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pengeluaran Kas -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-2 border-start border-4 border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase tracking-wider">Total Pengeluaran Kas</span>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                        <i class="fa-solid fa-money-bill-transfer fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bolder text-danger mb-1">{{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                <div class="d-flex align-items-center text-muted font-xs">
                    <span class="badge bg-danger text-white rounded-pill px-2 py-0.5 me-1 fw-bold">Expense</span>
                    Beban operasional
                </div>
            </div>
        </div>
    </div>

    <!-- Laba Bersih Komersial -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-2 border-start border-4 {{ $netProfit >= 0 ? 'border-primary' : 'border-danger' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase tracking-wider">Laba Bersih Komersial</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                        <i class="fa-solid fa-scale-balanced fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bolder {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }} mb-1">
                    {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($netProfit, 0, ',', '.') }}
                </h4>
                <div class="d-flex align-items-center text-muted font-xs">
                    <span class="badge bg-primary text-white rounded-pill px-2 py-0.5 me-1 fw-bold">Net</span>
                    Pendapatan - Beban
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