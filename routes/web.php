<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinancialReportController;

// ==========================================
// 1. DASHBOARD UTAMA (MINI ERP)
// ==========================================
Route::get('/', [DocumentController::class, 'dashboard'])->name('dashboard');

// ==========================================
// 2. MANAGEMENT DOKUMEN (INVOICE / PO)
// ==========================================
Route::resource('documents', DocumentController::class);
Route::patch('/documents/{id}/update-status', [DocumentController::class, 'updateStatus'])->name('documents.update-status');
Route::post('/documents/export-coretax', [DocumentController::class, 'exportCoretax'])->name('documents.export_coretax');
Route::post('/documents/export-coretax-xml', [DocumentController::class, 'exportCoretaxXml'])->name('documents.export_coretax_xml');
Route::post('/documents/export-xml', [DocumentController::class, 'exportXml'])->name('documents.export_xml');
Route::get('/documents/{id}/pdf', [DocumentController::class, 'downloadPdf'])->name('documents.pdf');
Route::get('/documents/{id}/delivery-note', [DocumentController::class, 'downloadDeliveryNote'])->name('documents.delivery_note');
Route::get('/documents/{id}/kwitansi', [DocumentController::class, 'downloadKwitansi'])->name('documents.kwitansi');
Route::post('/documents/{id}/duplicate', [DocumentController::class, 'duplicate'])->name('documents.duplicate');

// ==========================================
// 3. MASTER DATA & CUSTOMER
// ==========================================
Route::resource('customers', CustomerController::class);

// ==========================================
// 4. CHART OF ACCOUNTS (KATEGORI KEUANGAN & PAJAK)
// ==========================================
Route::resource('categories', CategoryController::class);

// ==========================================
// 5. TRANSAKSI KEUANGAN & ARUS KAS (MINI ERP)
// ==========================================
Route::resource('transactions', TransactionController::class);
Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'downloadReceipt'])->name('transactions.receipt');

// ==========================================
// 6. REPORTING ENGINE & FINANCIAL STATEMENTS
// ==========================================
Route::prefix('reports')->name('reports.')->group(function () {
    // 6.1 Laporan Keuangan Profesional (Executive Summary, Ledger, Invoicing Recap)
    Route::get('/financial-statement', [FinancialReportController::class, 'financialStatement'])->name('financial_statement');
    Route::get('/financial-statement/pdf', [FinancialReportController::class, 'exportFinancialStatementPdf'])->name('financial_statement.pdf');
    Route::get('/financial-statement/excel', [FinancialReportController::class, 'exportFinancialStatementExcel'])->name('financial_statement.excel');

    // 6.2 Laba Rugi Komersial & Rekonsiliasi Fiskal (SPT Tahunan)
    Route::get('/profit-loss', [FinancialReportController::class, 'profitAndLoss'])->name('profit_loss');
    Route::get('/profit-loss/pdf', [FinancialReportController::class, 'exportProfitAndLossPdf'])->name('profit_loss.pdf');
    Route::get('/profit-loss/excel', [FinancialReportController::class, 'exportProfitAndLossExcel'])->name('profit_loss.excel');
});

// ==========================================
// 7. PENGATURAN PERUSAHAAN (SETTINGS)
// ==========================================
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');