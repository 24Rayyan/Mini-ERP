<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| Web Routes - Mini ERP DCI (Phase 2 with Dynamic Landing Page)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC / GUEST ROUTES (LOGIN)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// 2. PROTECTED MODULE ROUTES (AUTHENTICATED & RBAC)
// ==========================================
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD UTAMA & REDIRECTOR CERDAS ---
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', function () {
        $user = auth()->user();

        // 1. Jika user memiliki akses ke modul 'dashboard', tampilkan dashboard
        if ($user->hasAccess('dashboard')) {
            return app(DocumentController::class)->dashboard();
        }

        // 2. Mapping key modul di DB ke nama route landing page masing-masing
        $moduleRoutes = [
            'invoices'        => 'documents.index',
            'expenses'        => 'transactions.index',
            'reports'         => 'reports.financial_statement',
            'user_management' => 'management.users.index',
            'settings'        => 'settings.index', // <-- Ditambahkan ke smart redirector
        ];

        // 3. Iterasi modul milik role user & redirect ke modul pertama yang cocok
        if ($user->role && $user->role->modules) {
            foreach ($user->role->modules as $module) {
                if (isset($moduleRoutes[$module->key]) && $user->hasAccess($module->key)) {
                    return redirect()->route($moduleRoutes[$module->key]);
                }
            }
        }

        // 4. Fallback jika user sama sekali tidak memiliki akses modul aktif
        abort(403, 'Anda tidak memiliki hak akses untuk membuka modul apa pun.');

    })->name('dashboard');

    // --- MANAGEMENT DOKUMEN (INVOICE / PO) ---
    Route::middleware(['module:invoices'])->group(function () {
        Route::resource('documents', DocumentController::class);
        Route::patch('/documents/{id}/update-status', [DocumentController::class, 'updateStatus'])->name('documents.update-status');
        Route::post('/documents/export-coretax', [DocumentController::class, 'exportCoretax'])->name('documents.export_coretax');
        Route::post('/documents/export-coretax-xml', [DocumentController::class, 'exportCoretaxXml'])->name('documents.export_coretax_xml');
        Route::post('/documents/export-xml', [DocumentController::class, 'exportXml'])->name('documents.export_xml');
        Route::get('/documents/{id}/pdf', [DocumentController::class, 'downloadPdf'])->name('documents.pdf');
        Route::get('/documents/{id}/delivery-note', [DocumentController::class, 'downloadDeliveryNote'])->name('documents.delivery_note');
        Route::get('/documents/{id}/kwitansi', [DocumentController::class, 'downloadKwitansi'])->name('documents.kwitansi');
        Route::post('/documents/{id}/duplicate', [DocumentController::class, 'duplicate'])->name('documents.duplicate');

        // Master Data Customer (Satu paket akses dengan dokumen/invoice)
        Route::resource('customers', CustomerController::class);
    });

    // --- TRANSAKSI KEUANGAN & PENGELUARAN (EXPENSES / ARUS KAS) ---
    Route::middleware(['module:expenses'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('transactions', TransactionController::class);
        Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'downloadReceipt'])->name('transactions.receipt');
    });

    // --- REPORTING ENGINE & FINANCIAL STATEMENTS (REPORTS) ---
    Route::middleware(['module:reports'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/financial-statement', [FinancialReportController::class, 'financialStatement'])->name('financial_statement');
        Route::get('/financial-statement/pdf', [FinancialReportController::class, 'exportFinancialStatementPdf'])->name('financial_statement.pdf');
        Route::get('/financial-statement/excel', [FinancialReportController::class, 'exportFinancialStatementExcel'])->name('financial_statement.excel');

        Route::get('/profit-loss', [FinancialReportController::class, 'profitAndLoss'])->name('profit_loss');
        Route::get('/profit-loss/pdf', [FinancialReportController::class, 'exportProfitAndLossPdf'])->name('profit_loss.pdf');
        Route::get('/profit-loss/excel', [FinancialReportController::class, 'exportProfitAndLossExcel'])->name('profit_loss.excel');
    });

    // --- PENGATURAN PERUSAHAAN (SETTINGS) ---
    Route::middleware(['module:settings'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // --- MODUL MANAGEMENT USER & ROLE (USER ACCESS MODULE) ---
    Route::middleware(['module:user_management'])->prefix('management')->name('management.')->group(function () {
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Roles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::post('/roles/{role}/grant-modules', [RoleController::class, 'grantModules'])->name('roles.grant-modules');
    });
});