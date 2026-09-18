@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">
            Cashflow Management
        </h3>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <div class="btn-group shadow-sm">
            <a href="{{ route('transactions.create', ['type' => 'expense']) }}" class="btn btn-danger">
                <i class="fa-solid fa-minus-circle me-1.5"></i> Catat Pengeluaran
            </a>
            <a href="{{ route('transactions.create', ['type' => 'income']) }}" class="btn btn-success">
                <i class="fa-solid fa-plus-circle me-1.5"></i> Catat Pemasukan
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
    <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Executive Cashflow Cards Container -->
<div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
    <!-- Header Banner dengan Gradient Premium -->
    <div class="card-header border-0 p-4 p-lg-4 text-white position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold mb-0 text-white">Executive Cashflow Summary</h4>
            </div>
        </div>
    </div>

    <!-- Body / Content KPI Cards -->
    <div class="card-body p-4 bg-light bg-opacity-50">
        <div class="row g-3">
            
            <!-- 1. Total Pemasukan Kas -->
            <div class="col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 bg-success" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Total Pemasukan Kas</span>
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid fa-arrow-trend-up me-1"></i>{{ $transactions->where('type', 'income')->count() }} Transaksi
                                </span>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-arrow-trend-up fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-dark mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($totalIncome, 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">Total akumulasi kas masuk</p>
                    </div>
                </div>
            </div>

            <!-- 2. Total Pengeluaran Kas -->
            <div class="col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 bg-danger" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Total Pengeluaran Kas</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid fa-arrow-trend-down me-1"></i>{{ $transactions->where('type', 'expense')->count() }} Transaksi
                                </span>
                            </div>
                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-arrow-trend-down fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-dark mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($totalExpense, 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">Total akumulasi kas keluar</p>
                    </div>
                </div>
            </div>

            <!-- 3. Arus Kas Bersih (Net Cashflow) -->
            <div class="col-sm-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    <div class="position-absolute top-0 start-0 bottom-0 {{ $netCashflow >= 0 ? 'bg-primary' : 'bg-warning' }}" style="width: 4px;"></div>
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-uppercase font-xs fw-bold text-muted tracking-wider d-block">Arus Kas Bersih</span>
                                <span class="badge {{ $netCashflow >= 0 ? 'bg-primary-subtle text-primary border-primary' : 'bg-warning-subtle text-warning border-warning' }} border border-opacity-25 rounded-pill font-xs px-2 py-0.5 mt-1">
                                    <i class="fa-solid fa-wallet me-1"></i>Net Cashflow
                                </span>
                            </div>
                            <div class="{{ $netCashflow >= 0 ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-10 text-warning' }} rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-wallet fs-5"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder {{ $netCashflow >= 0 ? 'text-primary' : 'text-danger' }} mb-1 tracking-tight">
                            {{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($netCashflow, 0, ',', '.') }}
                        </h4>
                        <p class="text-muted font-xs mb-0">Kas Masuk - Kas Keluar Periode Ini</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('transactions.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Periode Bulan/Tahun</label>
                <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Periode</option>
                    @foreach($availableMonths as $monthOption)
                        <option value="{{ $monthOption->value }}" {{ request('period') == $monthOption->value ? 'selected' : '' }}>
                            {{ $monthOption->label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Tipe Transaksi</label>
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan (Income)</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Kategori Akun (COA)</label>
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            [{{ $category->code }}] {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label font-xs fw-bold text-muted text-uppercase mb-1">Metode Bayar</label>
                <select name="payment_method" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Metode</option>
                    <option value="Bank Transfer" {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash / Tunai</option>
                    <option value="e-Wallet" {{ request('payment_method') == 'e-Wallet' ? 'selected' : '' }}>e-Wallet / QRIS</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset Filter
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Transactions Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 140px;">No. Transaksi</th>
                        <th style="width: 110px;">Tanggal</th>
                        <th>Kategori & Klasifikasi</th>
                        <th>Keterangan / Relasi</th>
                        <th style="width: 120px;">Metode</th>
                        <th class="text-end" style="width: 160px;">Jumlah (Rp)</th>
                        <th class="text-center" style="width: 100px;">Bukti</th>
                        <th class="text-center" style="width: 100px;">SPT Status</th>
                        <th class="text-end pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold font-mono text-dark" style="font-size: 0.8rem;">
                                {{ $trx->transaction_number }}
                            </span>
                            @if($trx->invoice_id)
                                <span class="badge bg-info-subtle text-info border border-info-subtle d-block mt-0.5" style="font-size: 0.65rem;">
                                    <i class="fa-solid fa-link me-1"></i> Inv #{{ $trx->document?->document_number }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark fw-semibold" style="font-size: 0.825rem;">
                                {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark font-sm">
                                [{{ $trx->category?->code }}] {{ $trx->category?->name }}
                            </div>
                            @if($trx->type === 'income')
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-xs">
                                    Pemasukan
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-xs">
                                    Pengeluaran
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark font-sm text-truncate" style="max-width: 250px;" title="{{ $trx->description }}">
                                {{ $trx->description ?? '-' }}
                            </div>
                            @if($trx->entertainmentDetail)
                                <small class="text-warning-emphasis fw-semibold font-xs d-block">
                                    <i class="fa-solid fa-users me-1"></i> Jamuan: {{ $trx->entertainmentDetail->attendee_name }} ({{ $trx->entertainmentDetail->attendee_company }})
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="fa-regular fa-credit-card me-1 text-muted"></i> {{ $trx->payment_method }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold {{ $trx->type === 'income' ? 'text-success' : 'text-danger' }}" style="font-size: 0.95rem;">
                                {{ $trx->type === 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($trx->receipt_file_path)
                                <a href="{{ route('transactions.receipt', $trx->id) }}" target="_blank" class="btn btn-sm btn-outline-info py-0.5 px-2 font-xs rounded-pill" title="Lihat Bukti Nota">
                                    <i class="fa-solid fa-receipt me-1"></i> Nota
                                </a>
                            @else
                                <span class="text-muted font-xs">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($trx->type === 'income')
                                <span class="badge bg-success-subtle text-success font-xs">Income</span>
                            @elseif($trx->category?->is_tax_deductible)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-xs" title="Dapat menjadi pengurang pajak SPT">
                                    Deductible
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-xs" title="Koreksi Positif Fiskal SPT">
                                    Koreksi
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if(!$trx->invoice_id)
                                <a href="{{ route('transactions.edit', $trx->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-light text-muted border font-xs" title="Transaksi otomatis dikunci oleh sistem invoice">
                                    <i class="fa-solid fa-lock me-1"></i> Auto
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fs-2 mb-2 d-block text-secondary"></i>
                            Belum ada transaksi kas untuk filter yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
