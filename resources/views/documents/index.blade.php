@extends('layouts.app')

@section('content')
<!-- Header Page & Breadcrumb SaaS Style -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted small">Dashboard</a></li>
                <li class="breadcrumb-item active small fw-semibold" aria-current="page">Daftar Dokumen</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0 tracking-tight">Kelola Dokumen</h3>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('documents.create') }}" class="btn btn-primary px-3.5 py-2 rounded-3 shadow-sm fw-semibold d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-plus fs-6"></i>
            <span>Buat Dokumen Baru</span>
        </a>
    </div>
</div>

<!-- Bulk Action Floating Bar -->
<div id="bulkActionBar" class="card border-0 shadow-lg rounded-4 mb-4 bg-dark text-white d-none">
    <div class="card-body p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                <span id="selectedCount">0</span> Faktur Dipilih
            </div>
            <span class="text-light small d-none d-md-inline">Pilih beberapa invoice untuk diekspor ke Format Resmi Coretax DJP.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-light rounded-3" id="btnClearSelection">
                Batal Pilih
            </button>
            <button type="button" class="btn btn-sm btn-success rounded-3 fw-bold px-3 d-inline-flex align-items-center gap-2 shadow-sm" id="btnOpenCoretaxModal">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Coretax DJP (.xlsx)</span>
            </button>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-4">
        
        <!-- TOOLBAR: FILTER BULAN & SEARCH -->
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
            <form action="{{ route('documents.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm filter-month-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-regular fa-calendar-days"></i>
                    </span>
                    <select name="period" class="form-select form-select-sm border-start-0 fw-semibold text-dark" style="cursor: pointer;" onchange="this.form.submit()">
                        <option value="">Semua Periode / Bulan</option>
                        @foreach($availableMonths as $item)
                            <option value="{{ $item->value }}" {{ request('period') == $item->value ? 'selected' : '' }}>
                                {{ $item->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('period'))
                    <a href="{{ route('documents.index') }}" class="btn btn-sm btn-light text-secondary rounded-3 border" data-bs-toggle="tooltip" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-saas-table" id="myTable">
                <thead>
                    <tr>
                        <th width="4%" class="text-center">
                            <input type="checkbox" class="form-check-input" id="selectAllDocs" title="Pilih Semua Faktur">
                        </th>
                        <th width="20%">No. Dokumen</th>
                        <th width="22%">Customer</th>
                        <th width="16%">Total Tagihan</th>
                        <th width="14%">Status (Quick Change)</th>
                        <th width="12%" class="text-center">PDF Export</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                    <tr>
                        <!-- Checkbox Bulk Export (Invoice Only) -->
                        <td class="text-center">
                            @if($doc->type == 'INVOICE')
                                <input type="checkbox" class="form-check-input doc-checkbox" 
                                       value="{{ $doc->id }}" 
                                       data-docnum="{{ $doc->document_number }}"
                                       data-customer="{{ $doc->customer->name ?? '-' }}"
                                       data-subtotal="{{ (float) $doc->subtotal }}"
                                       data-tax="{{ (float) $doc->tax_amount }}"
                                       data-total="{{ (float) $doc->total_amount }}"
                                       data-has-tax-id="{{ (!empty($doc->customer->tax_id_number) || !empty($doc->customer->npwp)) ? '1' : '0' }}">
                            @else
                                <span class="text-muted small" title="Hanya INVOICE yang dapat diekspor ke Coretax">-</span>
                            @endif
                        </td>

                        <!-- No. Dokumen + Badge Tipe -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($doc->type == 'PO')
                                    <span class="badge-type badge-po">PO</span>
                                @else
                                    <span class="badge-type badge-inv">INV</span>
                                @endif
                                <span class="fw-semibold text-dark font-monospace tracking-wide">{{ $doc->document_number }}</span>
                            </div>
                        </td>

                        <!-- Customer -->
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-dark">{{ $doc->customer->name ?? 'Tanpa Nama' }}</span>
                                @if(!empty($doc->customer->tax_id_number))
                                    <span class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $doc->customer->tax_id_type ?? 'NPWP16' }}: {{ $doc->customer->tax_id_number }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- Total Tagihan / Nominal -->
                        <td>
                            <span class="fw-bold text-dark">{{ $setting->currency_symbol ?? 'Rp' }} {{ number_format($doc->total_amount, 0, ',', '.') }}</span>
                        </td>
                        
                        <!-- QUICK CHANGE STATUS VIA AJAX -->
                        <td>
                            <select class="form-select status-select fw-semibold" data-id="{{ $doc->id }}">
                                <option value="DRAFT" {{ $doc->status == 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                                <option value="SENT" {{ $doc->status == 'SENT' ? 'selected' : '' }}>SENT</option>
                                <option value="PAID" {{ $doc->status == 'PAID' ? 'selected' : '' }}>PAID</option>
                            </select>
                        </td>
                        
                        <!-- PDF EXPORT (Modern Button Group) -->
                        <td class="text-center">
                            <div class="action-btn-group justify-content-center">
                                <!-- PDF Utama -->
                                <a href="{{ route('documents.pdf', $doc->id) }}" class="action-icon-btn text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak PDF Utama">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>

                                @if($doc->type == 'INVOICE')
                                    <!-- Surat Jalan -->
                                    <a href="{{ route('documents.delivery_note', $doc->id) }}" class="action-icon-btn text-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak Surat Jalan">
                                        <i class="fa-solid fa-truck-fast"></i>
                                    </a>

                                    <!-- Kwitansi -->
                                    <a href="{{ route('documents.kwitansi', $doc->id) }}" class="action-icon-btn text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak Kwitansi">
                                        <i class="fa-solid fa-receipt"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                        
                        <!-- ACTIONS (Clean Neutral Design) -->
                        <td class="text-center">
                            <div class="action-btn-group justify-content-center">
                                <!-- Duplicate -->
                                <a href="{{ route('documents.duplicate', $doc->id) }}" class="action-icon-btn text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Duplikasi">
                                    <i class="fa-solid fa-copy"></i>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('documents.edit', $doc->id) }}" class="action-icon-btn text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                
                                <!-- Delete -->
                                <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="action-icon-btn text-danger btn-delete" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Quick Check Coretax Export -->
<div class="modal fade" id="coretaxExportModal" tabindex="-1" aria-labelledby="coretaxExportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('documents.export_coretax') }}" method="POST" id="formExportCoretax">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div>
                        <div class="badge bg-success-subtle text-success fw-bold px-2.5 py-1.5 rounded-pill mb-1 small">
                            <i class="fa-solid fa-file-excel me-1"></i> Coretax DJP Ready
                        </div>
                        <h5 class="modal-title fw-bold text-dark" id="coretaxExportModalLabel">Export Faktur Pajak Keluaran (FK)</h5>
                        <p class="text-muted small mb-0">File XLSX multi-sheet sesuai format resmi Coretax DJP (NPWP 16 Digit & NITKU 22 Digit).</p>
                    </div>
                    <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Quick Stats Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3 bg-light border text-center">
                                <span class="text-muted small fw-semibold d-block">Jumlah Faktur</span>
                                <span class="fs-4 fw-bold text-dark" id="modalCount">0</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3 bg-light border text-center">
                                <span class="text-muted small fw-semibold d-block">Total DPP (Dasar Pengenaan)</span>
                                <span class="fs-5 fw-bold text-primary" id="modalSubtotal">Rp 0</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3 bg-light border text-center">
                                <span class="text-muted small fw-semibold d-block">Total PPN (Pajak)</span>
                                <span class="fs-5 fw-bold text-success" id="modalTax">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Warning if missing Tax ID -->
                    <div id="modalTaxIdWarning" class="alert alert-warning border-0 rounded-3 d-none mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-warning fs-5"></i>
                            <div class="small">
                                <strong>Perhatian:</strong> Ada <span id="missingTaxIdCount" class="fw-bold">0</span> invoice dengan Customer yang belum memiliki Nomor ID Perpajakan (NPWP/NIK). Sistem akan mengisi default '0000000000000000' (0 16-digit).
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="card border rounded-3 p-3 bg-body-tertiary mb-3">
                        <h6 class="fw-bold text-dark mb-2 small text-uppercase tracking-wide">Pengaturan Tanggal Faktur Pajak</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Tanggal Faktur Pajak (Opsional)</label>
                                <input type="date" name="tax_invoice_date" class="form-control form-control-sm rounded-3">
                                <span class="text-muted" style="font-size: 0.75rem;">Kosongkan jika ingin menggunakan tanggal asli dari masing-masing Invoice.</span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Kode Transaksi Faktur</label>
                                <div class="form-control form-control-sm bg-white text-muted">
                                    <span class="small">Mengikuti Kode Transaksi masing-masing invoice / default customer (040/020/010).</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected IDs Hidden Container -->
                    <div id="modalHiddenIds"></div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-3 px-3 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-excel fs-6"></i>
                        <span>Download Template Excel Coretax (.xlsx)</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Typography & Layout Rules */
    .tracking-tight { letter-spacing: -0.025em; }
    .tracking-wide { letter-spacing: 0.03em; }

    .custom-saas-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .custom-saas-table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .custom-saas-table tbody td {
        padding: 0.875rem 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    /* Filter Month Component */
    .filter-month-group {
        min-width: 220px;
    }
    .filter-month-group .input-group-text,
    .filter-month-group select {
        border-color: #cbd5e1;
    }
    .filter-month-group select:focus {
        border-color: #3b82f6;
        box-shadow: none;
    }

    /* Type Badges */
    .badge-type {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        letter-spacing: 0.05em;
    }
    .badge-po {
        background-color: #e0f2fe;
        color: #0369a1;
    }
    .badge-inv {
        background-color: #dcfce7;
        color: #15803d;
    }

    /* Action Buttons UI Optimization */
    .action-btn-group {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background-color: #f8fafc;
        padding: 3px 6px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .action-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        border: none;
        background: transparent;
        font-size: 0.875rem;
        transition: all 0.15s ease-in-out;
        text-decoration: none;
    }
    .action-icon-btn:hover {
        background-color: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transform: translateY(-1px);
    }

    /* Status Select Pill Styles */
    select.status-select {
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        padding: 0.3rem 1.75rem 0.3rem 0.75rem !important;
        border-radius: 20px !important;
        box-shadow: none !important;
        cursor: pointer;
        width: 125px;
        transition: all 0.2s;
    }
    select.status-select.status-draft {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
    }
    select.status-select.status-sent {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }
    select.status-select.status-paid {
        background-color: #f0fdf4 !important;
        color: #15803d !important;
        border: 1px solid #bbf7d0 !important;
    }

    /* DataTables Control UI Overhaul */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1.25rem;
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 0.35rem 1.8rem 0.35rem 0.75rem !important;
        font-size: 0.85rem !important;
        color: #334155 !important;
        outline: none;
        margin: 0 6px;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 0.35rem 0.75rem !important;
        font-size: 0.85rem !important;
        outline: none;
        margin-left: 8px;
    }
    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }
</style>

<script>
    function updateSelectColor(elem) {
        let val = $(elem).val();
        $(elem).removeClass('status-draft status-sent status-paid');
        if (val === 'DRAFT') {
            $(elem).addClass('status-draft');
        } else if (val === 'SENT') {
            $(elem).addClass('status-sent');
        } else if (val === 'PAID') {
            $(elem).addClass('status-paid');
        }
    }

    $(document).ready(function() {
        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        $('.status-select').each(function() {
            updateSelectColor(this);
        });

        // Bulk Action & Coretax Export Handler
        const dataTable = $('#myTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [0, 4, 5, 6] }
            ]
        });

        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function updateBulkBar() {
            let checkedBoxes = $('.doc-checkbox:checked');
            let count = checkedBoxes.length;

            if (count > 0) {
                $('#selectedCount').text(count);
                $('#bulkActionBar').removeClass('d-none');
            } else {
                $('#bulkActionBar').addClass('d-none');
                $('#selectAllDocs').prop('checked', false);
            }
        }

        // Select All handler
        $('#selectAllDocs').on('change', function() {
            let isChecked = $(this).is(':checked');
            $('.doc-checkbox').prop('checked', isChecked);
            updateBulkBar();
        });

        // Individual checkbox handler
        $(document).on('change', '.doc-checkbox', function() {
            let totalCheckboxes = $('.doc-checkbox').length;
            let checkedCount = $('.doc-checkbox:checked').length;
            
            $('#selectAllDocs').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCount);
            updateBulkBar();
        });

        // Clear selection handler
        $('#btnClearSelection').on('click', function() {
            $('.doc-checkbox').prop('checked', false);
            $('#selectAllDocs').prop('checked', false);
            updateBulkBar();
        });

        // Open Coretax Modal & calculate summary
        $('#btnOpenCoretaxModal').on('click', function() {
            let checkedBoxes = $('.doc-checkbox:checked');
            let count = checkedBoxes.length;

            if (count === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Dokumen',
                    text: 'Pilih minimal satu faktur / invoice untuk diekspor ke Coretax!'
                });
                return;
            }

            let totalSubtotal = 0;
            let totalTax = 0;
            let missingTaxId = 0;
            let hiddenInputsHtml = '';

            checkedBoxes.each(function() {
                let id = $(this).val();
                let subtotal = parseFloat($(this).data('subtotal')) || 0;
                let tax = parseFloat($(this).data('tax')) || 0;
                let hasTaxId = $(this).data('has-tax-id') == '1';

                totalSubtotal += subtotal;
                totalTax += tax;
                if (!hasTaxId) {
                    missingTaxId++;
                }

                hiddenInputsHtml += `<input type="hidden" name="document_ids[]" value="${id}">`;
            });

            $('#modalCount').text(count);
            $('#modalSubtotal').text(formatRupiah(totalSubtotal));
            $('#modalTax').text(formatRupiah(totalTax));
            $('#modalHiddenIds').html(hiddenInputsHtml);

            if (missingTaxId > 0) {
                $('#missingTaxIdCount').text(missingTaxId);
                $('#modalTaxIdWarning').removeClass('d-none');
            } else {
                $('#modalTaxIdWarning').addClass('d-none');
            }

            let modal = new bootstrap.Modal(document.getElementById('coretaxExportModal'));
            modal.show();
        });

        // Quick status update AJAX
        $(document).on('change', '.status-select', function() {
            let docId = $(this).data('id');
            let newStatus = $(this).val();
            let selectElem = this;

            updateSelectColor(selectElem);

            $.ajax({
                url: `/documents/${docId}/update-status`,
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Update',
                        text: 'Terjadi kesalahan saat memperbarui status dokumen!'
                    });
                }
            });
        });

        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Dokumen yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush