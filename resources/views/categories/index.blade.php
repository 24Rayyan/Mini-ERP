@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-tags text-primary me-2"></i> Chart of Accounts (COA) & Kategori Pajak
        </h3>
        <p class="text-muted mb-0 font-sm">
            Klasifikasi akun perkiraan keuangan untuk pembukuan komersial dan rekonsiliasi fiskal SPT Tahunan.
        </p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fa-solid fa-plus-circle me-1.5"></i> Tambah Akun Baru
        </button>
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

<!-- Quick Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fw-semibold font-xs text-uppercase">Total Akun Terdaftar</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1">{{ $categories->count() }} Akun</h4>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="fa-solid fa-folder-tree fs-5"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fw-semibold font-xs text-uppercase">Beban Fiskal (Deductible)</span>
                    <h4 class="fw-bold text-success mb-0 mt-1">
                        {{ $categories->where('type', 'expense')->where('is_tax_deductible', true)->count() }} Akun
                    </h4>
                    <small class="text-muted font-xs">Pengurang Penghasilan Kena Pajak</small>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="fa-solid fa-shield-halved fs-5"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fw-semibold font-xs text-uppercase">Koreksi Fiskal Positif</span>
                    <h4 class="fw-bold text-warning mb-0 mt-1">
                        {{ $categories->where('type', 'expense')->where('is_tax_deductible', false)->count() }} Akun
                    </h4>
                    <small class="text-muted font-xs">Non-Deductible pada SPT Tahunan</small>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                    <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable-category">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 120px;">Kode Akun</th>
                        <th>Nama Akun / Kategori</th>
                        <th style="width: 140px;">Tipe Akun</th>
                        <th style="width: 260px;">Status Fiskal SPT Tahunan</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width: 110px;">Transaksi</th>
                        <th class="text-end pe-4" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-dark bg-opacity-75 font-mono px-2.5 py-1.5 fw-bold">
                                {{ $category->code }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $category->name }}</span>
                            @if($category->code === '5-201')
                                <span class="badge bg-primary ms-1" style="font-size: 0.675rem;">Wajib Nominatif</span>
                            @endif
                        </td>
                        <td>
                            @if($category->type === 'income')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="fa-solid fa-arrow-down-left me-1"></i> Pendapatan
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="fa-solid fa-arrow-up-right me-1"></i> Beban / Biaya
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($category->type === 'income')
                                <span class="text-muted font-xs">
                                    <i class="fa-solid fa-check text-success me-1"></i> Penghasilan Bruto
                                </span>
                            @elseif($category->is_tax_deductible)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i class="fa-solid fa-circle-check me-1"></i> Deductible (Biaya Fiskal)
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">
                                    <i class="fa-solid fa-triangle-exclamation me-1 text-warning"></i> Non-Deductible (Koreksi Fiskal)
                                </span>
                            @endif
                        </td>
                        <td class="text-muted font-xs">
                            {{ $category->description ?? '-' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">
                                {{ $category->transactions_count }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-edit-category"
                                data-id="{{ $category->id }}"
                                data-code="{{ $category->code }}"
                                data-name="{{ $category->name }}"
                                data-type="{{ $category->type }}"
                                data-is_tax_deductible="{{ $category->is_tax_deductible ? '1' : '0' }}"
                                data-description="{{ $category->description }}">
                                <i class="fa-solid fa-pencil"></i>
                            </button>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $category->code }} - {{ $category->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $category->transactions_count > 0 ? 'disabled title=Tidak_bisa_dihapus_karena_ada_transaksi' : '' }}>
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open fs-2 mb-2 d-block text-secondary"></i>
                            Belum ada akun kategori yang dibuat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Akun Kategori -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white rounded-top-4 px-4 py-3">
                <h5 class="modal-title fw-bold" id="createCategoryModalLabel">
                    <i class="fa-solid fa-plus-circle text-primary me-2"></i> Tambah Akun Perkiraan (COA)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark font-sm">Kode Akun <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="Misal: 5-101" required>
                            <small class="text-muted font-xs">Format: [Kelompok]-[Nomor]</small>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark font-sm">Tipe Akun <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="expense">Beban / Pengeluaran (Expense)</option>
                                <option value="income">Pendapatan (Income)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark font-sm">Nama Akun <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Misal: Beban Alat Tulis Kantor" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark font-sm">Perlakuan Fiskal SPT Tahunan <span class="text-danger">*</span></label>
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="is_tax_deductible" id="deductibleYes" value="1" checked>
                                    <label class="form-check-label fw-semibold text-success font-sm" for="deductibleYes">
                                        <i class="fa-solid fa-check-circle me-1"></i> Deductible Expense (Dapat Mengurangi Penghasilan Kena Pajak)
                                    </label>
                                    <div class="text-muted font-xs ms-4">Biaya operasional 3M (Mendapatkan, Menagih, Memelihara penghasilan) yang sah secara pajak.</div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_tax_deductible" id="deductibleNo" value="0">
                                    <label class="form-check-label fw-semibold text-warning-emphasis font-sm" for="deductibleNo">
                                        <i class="fa-solid fa-triangle-exclamation me-1 text-warning"></i> Non-Deductible (Koreksi Fiskal Positif pada SPT)
                                    </label>
                                    <div class="text-muted font-xs ms-4">Biaya pribadi, denda pajak, sumbangan non-daftar, atau biaya tanpa bukti sah.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark font-sm">Keterangan / Catatan</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Catatan peruntukan akun ini..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-save me-1.5"></i> Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Akun Kategori -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white rounded-top-4 px-4 py-3">
                <h5 class="modal-title fw-bold" id="editCategoryModalLabel">
                    <i class="fa-solid fa-pencil text-primary me-2"></i> Edit Akun Perkiraan (COA)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark font-sm">Kode Akun <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit_code" class="form-control" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark font-sm">Tipe Akun <span class="text-danger">*</span></label>
                            <select name="type" id="edit_type" class="form-select" required>
                                <option value="expense">Beban / Pengeluaran (Expense)</option>
                                <option value="income">Pendapatan (Income)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark font-sm">Nama Akun <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark font-sm">Perlakuan Fiskal SPT Tahunan <span class="text-danger">*</span></label>
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="is_tax_deductible" id="edit_deductibleYes" value="1">
                                    <label class="form-check-label fw-semibold text-success font-sm" for="edit_deductibleYes">
                                        <i class="fa-solid fa-check-circle me-1"></i> Deductible Expense (Dapat Mengurangi Penghasilan Kena Pajak)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_tax_deductible" id="edit_deductibleNo" value="0">
                                    <label class="form-check-label fw-semibold text-warning-emphasis font-sm" for="edit_deductibleNo">
                                        <i class="fa-solid fa-triangle-exclamation me-1 text-warning"></i> Non-Deductible (Koreksi Fiskal Positif)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark font-sm">Keterangan / Catatan</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-save me-1.5"></i> Perbarui Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-edit-category').on('click', function() {
            var id = $(this).data('id');
            var code = $(this).data('code');
            var name = $(this).data('name');
            var type = $(this).data('type');
            var isDeductible = $(this).data('is_tax_deductible');
            var description = $(this).data('description');

            $('#editCategoryForm').attr('action', '/categories/' + id);
            $('#edit_code').val(code);
            $('#edit_name').val(name);
            $('#edit_type').val(type);
            $('#edit_description').val(description);

            if (isDeductible == '1') {
                $('#edit_deductibleYes').prop('checked', true);
            } else {
                $('#edit_deductibleNo').prop('checked', true);
            }

            var modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            modal.show();
        });
    });
</script>
@endpush
