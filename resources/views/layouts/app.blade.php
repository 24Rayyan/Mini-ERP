<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $globalSetting = \App\Models\Setting::getSetting();
    @endphp

    <title>{{ $globalSetting->company_name ?? 'Mini ERP' }} — Financial & Invoicing System</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 Pro/Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 CSS & Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        :root {
            --erp-primary: #1e40af;
            --erp-primary-hover: #1d4ed8;
            --erp-primary-light: #eff6ff;
            --erp-secondary: #0f172a;
            --erp-bg: #f8fafc;
            --erp-surface: #ffffff;
            --erp-border: #e2e8f0;
            --erp-text-main: #1e293b;
            --erp-text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--erp-bg);
            color: var(--erp-text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* --- MODERN MINIMALIST NAVBAR --- */
        .navbar-custom {
            background-color: #0b1329 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px -2px rgba(11, 19, 41, 0.35);
            backdrop-filter: blur(12px);
        }

        .navbar-brand-logo {
            max-height: 32px;
            width: auto;
            object-fit: contain;
        }

        .brand-text {
            font-weight: 800;
            letter-spacing: -0.3px;
            font-size: 1.1rem;
            color: #ffffff;
        }

        .brand-badge {
            font-size: 0.65rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Nav Links */
        .nav-link-custom {
            color: #94a3b8 !important;
            font-weight: 600;
            font-size: 0.86rem;
            padding: 0.5rem 0.95rem !important;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            margin: 0 2px;
            display: inline-flex;
            align-items: center;
        }

        .nav-link-custom:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .nav-link-custom.active {
            color: #ffffff !important;
            background-color: #1e40af;
            box-shadow: 0 2px 8px rgba(30, 64, 175, 0.35);
        }

        /* Pulse Indicator */
        .pulse-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* --- GLOBAL CARD & CONTAINER STYLING --- */
        .card {
            border: 1px solid var(--erp-border);
            border-radius: 14px;
            background: var(--erp-surface);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--erp-border);
            padding: 1rem 1.25rem;
            font-weight: 700;
        }

        /* Modern Form Controls */
        .form-control, .form-select {
            border-color: #cbd5e1;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            border-radius: 8px;
            color: #0f172a;
            transition: all 0.15s ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.15rem;
            font-size: 0.875rem;
            transition: all 0.15s ease-in-out;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .btn-primary {
            background-color: var(--erp-primary);
            border-color: var(--erp-primary);
        }

        .btn-primary:hover {
            background-color: var(--erp-primary-hover);
            border-color: var(--erp-primary-hover);
        }

        /* Status Select & Badges */
        select.status-select {
            font-size: 0.775rem !important;
            font-weight: 700 !important;
            padding: 0.3rem 1.5rem 0.3rem 0.75rem !important;
            border-radius: 50rem !important;
            cursor: pointer;
            box-shadow: none !important;
        }

        select.status-select.status-draft {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
        }

        select.status-select.status-sent {
            background-color: #eff6ff !important;
            color: #1e40af !important;
            border: 1px solid #93c5fd !important;
        }

        select.status-select.status-paid {
            background-color: #ecfdf5 !important;
            color: #065f46 !important;
            border: 1px solid #6ee7b7 !important;
        }

        /* Modern Tables */
        .table {
            font-size: 0.85rem;
            color: #1e293b;
        }

        .table thead th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.725rem;
            letter-spacing: 0.6px;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--erp-border);
            padding: 0.75rem 1rem;
        }

        .table tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Sticky Footer */
        footer {
            margin-top: auto;
            background-color: #ffffff;
            border-top: 1px solid var(--erp-border);
            color: #64748b;
            font-size: 0.8rem;
        }

        /* Minimalist Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION HEADER BAR -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-2.5 sticky-top">
        <div class="container-fluid px-lg-5">
            <!-- Brand & Logo -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                @if($globalSetting->company_logo && file_exists(public_path('storage/' . $globalSetting->company_logo)))
                    <img src="{{ asset('storage/' . $globalSetting->company_logo) }}" alt="Logo" class="navbar-brand-logo">
                @else
                    <div class="bg-primary text-white rounded-3 p-1 px-2 fw-bolder font-monospace" style="font-size: 0.95rem;">
                        <i class="fa-solid fa-cube me-1"></i> ERP
                    </div>
                @endif
                <div class="d-flex flex-column">
                    <span class="brand-text">{{ Str::limit($globalSetting->company_name ?? 'Mini ERP', 25) }}</span>
                </div>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <!-- 1. Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-chart-pie me-1.5"></i> Dashboard
                        </a>
                    </li>

                    <!-- 2. Invoicing & Dokumen Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-custom dropdown-toggle {{ request()->routeIs('documents.*') || request()->routeIs('customers.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-file-invoice-dollar me-1.5"></i> Invoicing & PO
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark border-0 shadow-lg" style="background-color: #0b1329; border-radius: 12px; min-width: 250px; border: 1px solid rgba(255,255,255,0.08);">
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('documents.create') ? 'active bg-primary' : '' }}" href="{{ route('documents.create') }}">
                                    <i class="fa-solid fa-plus-circle me-2 text-primary"></i> Buat Invoice / PO Baru
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('documents.index') ? 'active bg-primary' : '' }}" href="{{ route('documents.index') }}">
                                    <i class="fa-solid fa-folder-open me-2 text-info"></i> Kelola Semua Dokumen
                                </a>
                            </li>
                            <li><hr class="dropdown-divider border-secondary opacity-25"></li>
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('customers.*') ? 'active bg-primary' : '' }}" href="{{ route('customers.index') }}">
                                    <i class="fa-solid fa-users me-2 text-success"></i> Direktori Customer
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 3. Keuangan & Arus Kas Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-custom dropdown-toggle {{ request()->routeIs('transactions.*') || request()->routeIs('categories.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-money-bill-transfer me-1.5"></i> Buku Kas & Transaksi
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark border-0 shadow-lg" style="background-color: #0b1329; border-radius: 12px; min-width: 260px; border: 1px solid rgba(255,255,255,0.08);">
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('transactions.index') ? 'active bg-primary' : '' }}" href="{{ route('transactions.index') }}">
                                    <i class="fa-solid fa-receipt me-2 text-info"></i> Jurnal Transaksi Kas
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('transactions.create', ['type' => 'expense']) }}">
                                    <i class="fa-solid fa-minus-circle me-2 text-danger"></i> Catat Pengeluaran (Expense)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('transactions.create', ['type' => 'income']) }}">
                                    <i class="fa-solid fa-plus-circle me-2 text-success"></i> Catat Pemasukan (Income)
                                </a>
                            </li>
                            <li><hr class="dropdown-divider border-secondary opacity-25"></li>
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('categories.*') ? 'active bg-primary' : '' }}" href="{{ route('categories.index') }}">
                                    <i class="fa-solid fa-tags me-2 text-warning"></i> Master Akun & COA
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 4. Laporan Keuangan Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-custom dropdown-toggle {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-chart-line me-1.5"></i> Laporan Keuangan
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark border-0 shadow-lg" style="background-color: #0b1329; border-radius: 12px; min-width: 280px; border: 1px solid rgba(255,255,255,0.08);">
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('reports.financial_statement*') ? 'active bg-primary' : '' }}" href="{{ route('reports.financial_statement') }}">
                                    <i class="fa-solid fa-file-contract me-2 text-info"></i> Laporan Keuangan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('reports.profit_loss*') ? 'active bg-primary' : '' }}" href="{{ route('reports.profit_loss') }}">
                                    <i class="fa-solid fa-scale-balanced me-2 text-primary"></i> Laba Rugi Komersial & Fiskal
                                </a>
                            </li>
                            {{-- <li>
                                <a class="dropdown-item py-2 {{ request()->routeIs('reports.entertainment_nominative*') ? 'active bg-primary' : '' }}" href="{{ route('reports.entertainment_nominative') }}">
                                    <i class="fa-solid fa-champagne-glasses me-2 text-warning"></i> Lampiran Nominatif DJP
                                </a>
                            </li> --}}
                        </ul>
                    </li>

                    <!-- 5. Pengaturan Terpusat -->
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                            <i class="fa-solid fa-sliders me-1.5"></i> Pengaturan
                        </a>
                    </li>
                </ul>

                <!-- Live Status & Quick Details Widget -->
                {{-- <div class="d-none d-lg-flex align-items-center gap-2">
                    <div class="d-flex align-items-center bg-white bg-opacity-10 py-1.5 px-3 rounded-pill border border-white border-opacity-10 text-white font-xs" style="font-size: 0.775rem;">
                        <span class="pulse-dot me-2"></span>
                        <span class="fw-semibold">{{ $globalSetting->currency_code ?? 'IDR' }} (PPN {{ $globalSetting->default_tax_rate ?? 11 }}%)</span>
                    </div>
                </div> --}}
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="container-fluid px-lg-5 py-4">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="py-3 mt-auto">
        <div class="container-fluid px-lg-5 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <div>
                &copy; {{ date('Y') }} <strong>{{ $globalSetting->company_name ?? 'Mini ERP System' }}</strong>. All rights reserved.
            </div>
            <div class="text-muted small">
                <span>Versi 2.0 Mini ERP &bull; Realtime Accounting Engine</span>
            </div>
        </div>
    </footer>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Global Toast Notification Helper
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });

        // Flash message handling
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        // Dynamic status selector colorization
        function applyStatusColor(element) {
            let val = $(element).val();
            $(element).removeClass('status-draft status-sent status-paid');
            
            if (val === 'DRAFT') {
                $(element).addClass('status-draft');
            } else if (val === 'SENT') {
                $(element).addClass('status-sent');
            } else if (val === 'PAID') {
                $(element).addClass('status-paid');
            }
        }

        $(document).ready(function() {
            $('.status-select').each(function() {
                applyStatusColor(this);
            });

            $(document).on('change', '.status-select', function() {
                applyStatusColor(this);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>