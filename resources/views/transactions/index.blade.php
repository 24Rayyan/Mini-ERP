@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-money-bill-transfer text-primary me-2"></i> Transaksi Kas & Jurnal Keuangan
        </h3>
        <p class="text-muted mb-0 font-sm">
            Pencatatan arus kas masuk/keluar, lampiran bukti nota, dan integrasi otomatis invoice lunas.
        </p>
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

<!-- Executive Cashflow Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fw-semibold font-xs text-uppercase">Total Pemasukan Kas</span>
                    <h4 class="fw-bold text-success mb-0 mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                    <small class="text-muted font-xs">{{ $transactions->where('type', 'income')->count() }} transaksi masuk</small>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="fa-solid fa-arrow-trend-up fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white border-start border-4 border-danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fw-semibold font-xs text-uppercase">Total Pengeluaran Kas</span>
                    <h4 class="fw-bold text-danger mb-0 mt-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                    <small class="text-muted font-xs">{{ $transactions->where('type', 'expense')->count() }} transaksi keluar</small>
                </div>
                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                    <i class="fa-solid fa-arrow-trend-down fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white border-start border-4 {{ $netCashflow >= 0 ? 'border-primary' : 'border-warning' }}">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fw-semibold font-xs text-uppercase">Arus Kas Bersih (Net Cashflow)</span>
                    <h4 class="fw-bold {{ $netCashflow >= 0 ? 'text-primary' : 'text-danger' }} mb-0 mt-1">
                        Rp {{ number_format($netCashflow, 0, ',', '.') }}
                    </h4>
                    <small class="text-muted font-xs">Kas Masuk - Kas Keluar Periode Ini</small>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="fa-solid fa-wallet fs-4"></i>
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
