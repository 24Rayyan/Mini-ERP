<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Document;
use App\Models\Transaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['category', 'document.customer', 'entertainmentDetail'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        // Filter Periode
        $selectedYear = $request->filled('year') ? (int)$request->year : date('Y');
        $selectedMonth = $request->filled('month') ? (int)$request->month : null;

        if ($request->filled('period')) {
            [$selectedYear, $selectedMonth] = explode('-', $request->period);
        }

        $query->whereYear('transaction_date', $selectedYear);
        if ($selectedMonth) {
            $query->whereMonth('transaction_date', $selectedMonth);
        }

        // Filter Tipe (income / expense)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter Metode Pembayaran
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search text
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('code', 'LIKE', "%{$search}%");
                  });
            });
        }

        $transactions = $query->get();

        // Summary Cards
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netCashflow = $totalIncome - $totalExpense;

        $categories = Category::orderBy('code', 'asc')->get();

        // Daftar Periode Unik untuk Filter
        $availableMonths = Transaction::select('transaction_date')
            ->whereNotNull('transaction_date')
            ->orderBy('transaction_date', 'desc')
            ->get()
            ->map(function ($trx) {
                $date = Carbon::parse($trx->transaction_date);
                return (object) [
                    'year'  => $date->format('Y'),
                    'month' => $date->format('m'),
                    'value' => $date->format('Y-m'),
                    'label' => $date->translatedFormat('F Y'),
                ];
            })
            ->unique('value');

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'totalIncome',
            'totalExpense',
            'netCashflow',
            'selectedYear',
            'selectedMonth',
            'availableMonths'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $defaultType = $request->get('type', 'expense');
        $categories = Category::orderBy('code', 'asc')->get();
        $autoNumber = Transaction::generateTransactionNumber($defaultType, date('Y-m-d'));

        return view('transactions.create', compact('categories', 'defaultType', 'autoNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'              => 'required|in:income,expense',
            'category_id'       => 'required|exists:categories,id',
            'amount'            => 'required|numeric|min:1',
            'transaction_date'  => 'required|date',
            'payment_method'    => 'required|string',
            'description'       => 'nullable|string',
            'receipt_file'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
            
            // Validasi Lampiran Nominatif Entertainment jika diisi
            'event_date'        => 'nullable|date',
            'location'          => 'nullable|string|max:255',
            'attendee_name'     => 'nullable|string|max:255',
            'attendee_company'  => 'nullable|string|max:255',
            'attendee_position' => 'nullable|string|max:255',
            'purpose'           => 'nullable|string',
        ]);

        $category = Category::findOrFail($request->category_id);

        // Jika kategori adalah 5-201 (Jamuan/Entertainment), data nominatif wajib diisi
        if ($category->code === '5-201' && empty($request->attendee_name)) {
            return back()->withInput()->withErrors([
                'attendee_name' => 'Kategori Biaya Entertainment (5-201) wajib melengkapi rincian Daftar Nominatif (Nama Relasi, Instansi, dsb) untuk keperluan SPT Tahunan.'
            ]);
        }

        $entertainmentData = null;
        if (!empty($request->attendee_name)) {
            $entertainmentData = [
                'event_date'        => $request->event_date ?? $request->transaction_date,
                'location'          => $request->location ?? '-',
                'attendee_name'     => $request->attendee_name,
                'attendee_company'  => $request->attendee_company ?? '-',
                'attendee_position' => $request->attendee_position ?? '-',
                'purpose'           => $request->purpose ?? 'Hubungan Bisnis / Entertainment Klien',
            ];
        }

        $transactionData = [
            'type'             => $request->type,
            'category_id'      => $request->category_id,
            'amount'           => $request->amount,
            'transaction_date' => $request->transaction_date,
            'payment_method'   => $request->payment_method,
            'description'      => $request->description,
        ];

        $this->transactionService->createTransaction(
            $transactionData,
            $request->file('receipt_file'),
            $entertainmentData
        );

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat ke dalam jurnal keuangan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        $transaction->load(['category', 'entertainmentDetail', 'document']);
        $categories = Category::orderBy('code', 'asc')->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'amount'            => 'required|numeric|min:1',
            'transaction_date'  => 'required|date',
            'payment_method'    => 'required|string',
            'description'       => 'nullable|string',
            'receipt_file'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
            
            // Validasi Lampiran Nominatif Entertainment jika diisi
            'event_date'        => 'nullable|date',
            'location'          => 'nullable|string|max:255',
            'attendee_name'     => 'nullable|string|max:255',
            'attendee_company'  => 'nullable|string|max:255',
            'attendee_position' => 'nullable|string|max:255',
            'purpose'           => 'nullable|string',
        ]);

        $category = Category::findOrFail($request->category_id);

        if ($category->code === '5-201' && empty($request->attendee_name)) {
            return back()->withInput()->withErrors([
                'attendee_name' => 'Kategori Biaya Entertainment (5-201) wajib melengkapi rincian Daftar Nominatif (Nama Relasi, Instansi, dsb) untuk keperluan SPT Tahunan.'
            ]);
        }

        $entertainmentData = null;
        if (!empty($request->attendee_name)) {
            $entertainmentData = [
                'event_date'        => $request->event_date ?? $request->transaction_date,
                'location'          => $request->location ?? '-',
                'attendee_name'     => $request->attendee_name,
                'attendee_company'  => $request->attendee_company ?? '-',
                'attendee_position' => $request->attendee_position ?? '-',
                'purpose'           => $request->purpose ?? 'Hubungan Bisnis / Entertainment Klien',
            ];
        }

        $transactionData = [
            'type'             => $category->type,
            'category_id'      => $request->category_id,
            'amount'           => $request->amount,
            'transaction_date' => $request->transaction_date,
            'payment_method'   => $request->payment_method,
            'description'      => $request->description,
        ];

        $this->transactionService->updateTransaction(
            $transaction,
            $transactionData,
            $request->file('receipt_file'),
            $entertainmentData
        );

        return redirect()->route('transactions.index')->with('success', 'Data transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $this->transactionService->deleteTransaction($transaction);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus dari sistem.');
    }

    /**
     * Download or view receipt file
     */
    public function downloadReceipt(Transaction $transaction)
    {
        if (!$transaction->receipt_file_path || !Storage::disk('public')->exists($transaction->receipt_file_path)) {
            abort(404, 'Bukti nota/struk tidak ditemukan.');
        }

        return Storage::disk('public')->response($transaction->receipt_file_path);
    }
}
