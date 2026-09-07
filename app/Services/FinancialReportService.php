<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Document;
use App\Models\EntertainmentDetail;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinancialReportService
{
    /**
     * Laporan Keuangan Profesional & Komprehensif (Executive Summary, Transaction Ledger, Invoicing Recap)
     *
     * @param string $startDate (Format: Y-m-d)
     * @param string $endDate (Format: Y-m-d)
     * @param int|null $categoryId
     * @param string|null $type ('income' | 'expense' | null)
     * @return array
     */
    public function getDetailedFinancialReport(string $startDate, string $endDate, ?int $categoryId = null, ?string $type = null): array
    {
        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon = Carbon::parse($endDate)->endOfDay();

        // 1. Query Dasar Transaksi dalam Rentang Tanggal
        $trxQuery = Transaction::with(['category', 'document.customer', 'entertainmentDetail'])
            ->whereDate('transaction_date', '>=', $startCarbon->format('Y-m-d'))
            ->whereDate('transaction_date', '<=', $endCarbon->format('Y-m-d'));

        if ($categoryId) {
            $trxQuery->where('category_id', $categoryId);
        }

        if ($type) {
            $trxQuery->where('type', $type);
        }

        // Ambil transaksi berurutan secara kronologis untuk Buku Kas (Ledger)
        $transactions = $trxQuery->orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->get();

        // Hitung Saldo Berjalan (Running Balance) pada Buku Kas
        $runningBalance = 0;
        $ledgerEntries = $transactions->map(function ($trx) use (&$runningBalance) {
            $amountIn = $trx->type === 'income' ? (float) $trx->amount : 0;
            $amountOut = $trx->type === 'expense' ? (float) $trx->amount : 0;
            $runningBalance += ($amountIn - $amountOut);

            $refNumber = $trx->document ? $trx->document->document_number : $trx->transaction_number;
            $customerOrParty = $trx->document?->customer?->name 
                               ?? ($trx->entertainmentDetail ? $trx->entertainmentDetail->attendee_name . ' (' . $trx->entertainmentDetail->attendee_company . ')' : '-');

            return [
                'id'                 => $trx->id,
                'date'               => $trx->transaction_date,
                'formatted_date'     => $trx->transaction_date->format('d/m/Y'),
                'transaction_number' => $trx->transaction_number,
                'reference_number'   => $refNumber,
                'category_code'      => $trx->category?->code ?? 'N/A',
                'category_name'      => $trx->category?->name ?? 'Lain-lain',
                'is_tax_deductible'  => $trx->category ? (bool) $trx->category->is_tax_deductible : true,
                'type'               => $trx->type,
                'description'        => $trx->description ?? '-',
                'party_name'         => $customerOrParty,
                'payment_method'     => $trx->payment_method,
                'amount_in'          => $amountIn,
                'amount_out'         => $amountOut,
                'running_balance'    => $runningBalance,
                'receipt_path'       => $trx->receipt_file_path,
                'has_entertainment'  => (bool) $trx->entertainmentDetail,
            ];
        });

        // 2. REKAP BUKU INVOICING (PERIODE TERKAIT)
        $invoicesInPeriod = Document::where('type', 'INVOICE')
            ->whereDate('date', '>=', $startCarbon->format('Y-m-d'))
            ->whereDate('date', '<=', $endCarbon->format('Y-m-d'))
            ->get();

        $totalInvoicesCount = $invoicesInPeriod->count();
        $totalInvoicesAmount = (float) $invoicesInPeriod->sum('total_amount');

        $paidInvoices = $invoicesInPeriod->where('status', 'PAID');
        $paidInvoicesCount = $paidInvoices->count();
        $paidInvoicesAmount = (float) $paidInvoices->sum('total_amount');

        $sentInvoices = $invoicesInPeriod->where('status', 'SENT');
        $sentInvoicesCount = $sentInvoices->count();
        $sentInvoicesAmount = (float) $sentInvoices->sum('total_amount');

        $draftInvoices = $invoicesInPeriod->where('status', 'DRAFT');
        $draftInvoicesCount = $draftInvoices->count();
        $draftInvoicesAmount = (float) $draftInvoices->sum('total_amount');

        $unpaidReceivables = $sentInvoicesAmount + $draftInvoicesAmount;
        $collectionRate = $totalInvoicesAmount > 0 ? round(($paidInvoicesAmount / $totalInvoicesAmount) * 100, 1) : 0;

        // Total Piutang Aktif Keseluruhan Sistem (Global Outstanding)
        $globalOutstandingPiutang = (float) Document::where('type', 'INVOICE')->where('status', 'SENT')->sum('total_amount');

        // 3. EXECUTIVE SUMMARY METRICS
        $incomeTrx = $transactions->where('type', 'income');
        $expenseTrx = $transactions->where('type', 'expense');

        // Gabungkan Pembayaran Invoice PAID + Pemasukan Non-Invoice
        $paidInvoicesIncome = $paidInvoicesAmount;
        $otherIncome = (float) $incomeTrx->whereNull('invoice_id')->sum('amount');
        
        $totalIncome = $paidInvoicesIncome + $otherIncome;
        $totalExpense = (float) $expenseTrx->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $deductibleExpense = (float) $expenseTrx->filter(fn($t) => $t->category && $t->category->is_tax_deductible)->sum('amount');
        $nonDeductibleExpense = (float) $expenseTrx->filter(fn($t) => !$t->category || !$t->category->is_tax_deductible)->sum('amount');

        // 4. BREAKDOWN KATEGORI
        $categoryBreakdown = $transactions->groupBy('category_id')->map(function ($items) {
            $cat = $items->first()->category;
            $type = $items->first()->type;
            $sum = (float) $items->sum('amount');
            return [
                'category_id'       => $cat?->id,
                'code'              => $cat?->code ?? 'N/A',
                'name'              => $cat?->name ?? 'Lain-lain',
                'type'              => $type,
                'is_tax_deductible' => $cat ? (bool) $cat->is_tax_deductible : true,
                'count'             => $items->count(),
                'total_amount'      => $sum,
            ];
        })->sortBy('code')->values();

        return [
            'start_date'                  => $startCarbon->format('Y-m-d'),
            'end_date'                    => $endCarbon->format('Y-m-d'),
            'formatted_start_date'        => $startCarbon->translatedFormat('d F Y'),
            'formatted_end_date'          => $endCarbon->translatedFormat('d F Y'),
            'category_id'                 => $categoryId,
            'type'                        => $type,
            
            // Executive Summary
            'total_income'                => $totalIncome,
            'total_expense'               => $totalExpense,
            'net_profit'                  => $netProfit,
            'paid_invoices_income'        => $paidInvoicesIncome,
            'other_income'                => $otherIncome,
            'deductible_expense'          => $deductibleExpense,
            'non_deductible_expense'      => $nonDeductibleExpense,
            'global_outstanding_piutang'  => $globalOutstandingPiutang,

            // Invoicing Performance
            'invoicing_summary'           => [
                'total_count'         => $totalInvoicesCount,
                'total_amount'        => $totalInvoicesAmount,
                'paid_count'          => $paidInvoicesCount,
                'paid_amount'         => $paidInvoicesAmount,
                'sent_count'          => $sentInvoicesCount,
                'sent_amount'         => $sentInvoicesAmount,
                'draft_count'         => $draftInvoicesCount,
                'draft_amount'        => $draftInvoicesAmount,
                'unpaid_amount'       => $unpaidReceivables,
                'collection_rate'     => $collectionRate,
            ],

            // Ledger & Breakdown
            'ledger_entries'              => $ledgerEntries,
            'category_breakdown'          => $categoryBreakdown,
            'total_transactions_count'    => $transactions->count(),
        ];
    }

    /**
     * Menghitung Laporan Laba Rugi Komersial & Rekonsiliasi Fiskal (SPT Tahunan)
     *
     * @param int $year
     * @param int|null $month
     * @return array
     */
    public function getProfitAndLossReport(int $year, ?int $month = null): array
    {
        // 1. QUERY PENDAPATAN (INCOME) - MENGGABUNGKAN INVOICE PAID + TRANSAKSI NON-INVOICE
        $invoiceQuery = Document::where('type', 'INVOICE')
            ->where('status', 'PAID')
            ->whereYear('date', $year);

        if ($month) {
            $invoiceQuery->whereMonth('date', $month);
        }

        $paidInvoicesAmount = (float) $invoiceQuery->sum('total_amount');

        // Transaksi Pemasukan Tambahan (Manual / Non-Invoice)
        $incomeTrxQuery = Transaction::where('type', 'income')
            ->whereYear('transaction_date', $year)
            ->whereNull('invoice_id');

        if ($month) {
            $incomeTrxQuery->whereMonth('transaction_date', $month);
        }

        $otherIncomeTransactions = $incomeTrxQuery->with('category')->get();
        $otherIncomeAmount = (float) $otherIncomeTransactions->sum('amount');

        $totalIncome = $paidInvoicesAmount + $otherIncomeAmount;

        // Breakdown Pendapatan per Kategori
        $incomeByCategory = collect();

        if ($paidInvoicesAmount > 0) {
            $incomeByCategory->push([
                'category_id'        => null,
                'category_code'      => '4-100',
                'category_name'      => 'Pendapatan Invoice / Penjualan',
                'is_tax_deductible'  => true,
                'count'              => $invoiceQuery->count(),
                'total_amount'       => $paidInvoicesAmount,
            ]);
        }

        $otherCategories = $otherIncomeTransactions->groupBy('category_id')->map(function ($items) {
            $category = $items->first()->category;
            return [
                'category_id'        => $category?->id,
                'category_code'      => $category?->code ?? '4-999',
                'category_name'      => $category?->name ?? 'Pendapatan Lain-Lain',
                'is_tax_deductible'  => true,
                'count'              => $items->count(),
                'total_amount'       => (float) $items->sum('amount'),
            ];
        })->values();

        $incomeByCategory = $incomeByCategory->concat($otherCategories);

        // 2. BEBAN OPERASIONAL & BIAYA (EXPENSES)
        $expenseQuery = Transaction::with('category')
            ->where('type', 'expense')
            ->whereYear('transaction_date', $year);

        if ($month) {
            $expenseQuery->whereMonth('transaction_date', $month);
        }

        $expenseTransactions = $expenseQuery->get();
        $totalExpense = (float) $expenseTransactions->sum('amount');

        // Pisahkan Deductible (Biaya Fiskal) vs Non-Deductible (Koreksi Fiskal Positif)
        $deductibleTransactions = $expenseTransactions->filter(fn($trx) => $trx->category && $trx->category->is_tax_deductible);
        $nonDeductibleTransactions = $expenseTransactions->filter(fn($trx) => !$trx->category || !$trx->category->is_tax_deductible);

        $totalDeductibleExpense = (float) $deductibleTransactions->sum('amount');
        $totalNonDeductibleExpense = (float) $nonDeductibleTransactions->sum('amount');

        // Breakdown Beban per Kategori
        $expenseByCategory = $expenseTransactions->groupBy('category_id')->map(function ($items) {
            $category = $items->first()->category;
            $amount = (float) $items->sum('amount');
            $isDeductible = $category ? (bool) $category->is_tax_deductible : false;

            return [
                'category_id'        => $category?->id,
                'category_code'      => $category?->code ?? 'N/A',
                'category_name'      => $category?->name ?? 'Biaya Lain',
                'is_tax_deductible'  => $isDeductible,
                'count'              => $items->count(),
                'total_amount'       => $amount,
            ];
        })->values();

        // 3. KALKULASI LABA BERSIH
        $commercialNetProfit = $totalIncome - $totalExpense;
        $fiscalNetProfit = $totalIncome - $totalDeductibleExpense;

        // 4. TREND BULANAN (12 Bulan)
        $monthlyTrend = [];
        for ($m = 1; $m <= 12; $m++) {
            $mPaidInvoices = (float) Document::where('type', 'INVOICE')
                ->where('status', 'PAID')
                ->whereYear('date', $year)
                ->whereMonth('date', $m)
                ->sum('total_amount');

            $mOtherIncome = (float) Transaction::where('type', 'income')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $m)
                ->whereNull('invoice_id')
                ->sum('amount');

            $mIncome = $mPaidInvoices + $mOtherIncome;

            $mMonthExpenses = Transaction::where('type', 'expense')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $m)
                ->with('category')
                ->get();

            $mExpense = (float) $mMonthExpenses->sum('amount');
            $mDeductible = (float) $mMonthExpenses->filter(fn($t) => $t->category && $t->category->is_tax_deductible)->sum('amount');
            $mNonDeductible = (float) $mMonthExpenses->filter(fn($t) => !$t->category || !$t->category->is_tax_deductible)->sum('amount');

            $monthlyTrend[$m] = [
                'month_num'              => $m,
                'month_name'             => Carbon::create()->month($m)->translatedFormat('F'),
                'income'                 => $mIncome,
                'expense'                => $mExpense,
                'deductible_expense'     => $mDeductible,
                'non_deductible_expense' => $mNonDeductible,
                'commercial_profit'      => $mIncome - $mExpense,
                'fiscal_profit'          => $mIncome - $mDeductible,
            ];
        }

        return [
            'year'                         => $year,
            'month'                        => $month,
            'month_name'                   => $month ? Carbon::create()->month($month)->translatedFormat('F') : 'Sepanjang Tahun',
            'total_income'                 => $totalIncome,
            'total_expense'                => $totalExpense,
            'total_deductible_expense'     => $totalDeductibleExpense,
            'total_non_deductible_expense' => $totalNonDeductibleExpense,
            'fiscal_correction_positive'   => $totalNonDeductibleExpense,
            'commercial_net_profit'        => $commercialNetProfit,
            'fiscal_net_profit'            => $fiscalNetProfit,
            'income_categories'            => $incomeByCategory,
            'expense_categories'           => $expenseByCategory,
            'deductible_categories'        => $expenseByCategory->where('is_tax_deductible', true)->values(),
            'non_deductible_categories'    => $expenseByCategory->where('is_tax_deductible', false)->values(),
            'monthly_trend'                => $monthlyTrend,
            'transaction_count'            => $expenseTransactions->count() + $otherIncomeTransactions->count(),
        ];
    }

    /**
     * Mengambil Data Lampiran Khusus: Daftar Nominatif Biaya Entertainment & Promosi (SPT Tahunan DJP)
     *
     * @param int $year
     * @param int|null $month
     * @return Collection
     */
    public function getEntertainmentNominativeList(int $year, ?int $month = null): Collection
    {
        $query = EntertainmentDetail::with(['transaction.category'])
            ->whereHas('transaction', function ($q) use ($year, $month) {
                $q->whereYear('transaction_date', $year);
                if ($month) {
                    $q->whereMonth('transaction_date', $month);
                }
            })
            ->orderBy('event_date', 'asc');

        return $query->get();
    }
}   