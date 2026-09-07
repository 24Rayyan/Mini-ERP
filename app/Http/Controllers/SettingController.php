<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Helper privat untuk konversi angka bulan ke format Romawi.
     */
    private function getRomanMonth($month)
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romans[(int)$month] ?? 'I';
    }

    /**
     * Menampilkan halaman formulir konfigurasi sistem terpusat.
     */
    public function index()
    {
        // 1. Ambil data setting singleton
        $setting = Setting::getSetting();

        // 2. Ambil Informasi Bulan (Romawi) & Tahun Berjalan
        $currentMonth = date('n');
        $currentYear = date('Y');
        $romanMonth = $this->getRomanMonth($currentMonth);

        $invPrefix = $setting->invoice_prefix ?: 'INV-DCI';
        $poPrefix = $setting->po_prefix ?: 'PO';
        $sjPrefix = $setting->delivery_note_prefix ?: 'SJ-DCI';
        $kwtPrefix = $setting->kwitansi_prefix ?: 'KWT-DCI';

        // 3. Ambil Dokumen Terakhir di Bulan & Tahun Berjalan
        $lastInvoice = Document::where('type', 'INVOICE')
                               ->where('document_number', 'LIKE', "%/{$invPrefix}/{$romanMonth}/{$currentYear}")
                               ->orderBy('id', 'desc')
                               ->first();

        $lastPO = Document::where('type', 'PO')
                          ->where('document_number', 'LIKE', "%/{$poPrefix}/{$romanMonth}/{$currentYear}")
                          ->orderBy('id', 'desc')
                          ->first();

        return view('settings.index', compact(
            'setting',
            'lastInvoice',
            'lastPO',
            'romanMonth',
            'currentYear',
            'invPrefix',
            'poPrefix',
            'sjPrefix',
            'kwtPrefix'
        ));
    }

    /**
     * Memperbarui data konfigurasi sistem dan menyimpan file media.
     */
    public function update(Request $request)
    {
        // 1. Validasi Input & File
        $request->validate([
            'company_name'             => 'required|string|max:255',
            'company_tagline'          => 'nullable|string|max:255',
            'company_npwp'             => 'nullable|string|max:100',
            'company_npwp16'           => 'nullable|string|max:20',
            'company_nitku'            => 'nullable|string|max:25',
            'company_logo'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'company_stamp'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'company_email'            => 'nullable|email|max:255',
            'company_phone'            => 'nullable|string|max:50',
            'company_website'          => 'nullable|string|max:255',
            'company_address'          => 'nullable|string',
            'company_city'             => 'nullable|string|max:100',
            'company_bank_account'     => 'nullable|string',
            'invoice_prefix'           => 'required|string|max:50',
            'po_prefix'                => 'required|string|max:50',
            'delivery_note_prefix'     => 'nullable|string|max:50',
            'kwitansi_prefix'          => 'nullable|string|max:50',
            'default_tax_rate'         => 'required|numeric|min:0|max:100',
            'term_of_payment'          => 'nullable|integer|min:0',
            'currency_symbol'          => 'nullable|string|max:10',
            'currency_code'            => 'nullable|string|max:10',
            'signatory_name'           => 'nullable|string|max:255',
            'signatory_position'       => 'nullable|string|max:255',
            'signatory_city'           => 'nullable|string|max:100',
            'invoice_footer_notes'     => 'nullable|string',
            'receipt_footer_notes'     => 'nullable|string',
            'delivery_note_footer_notes'=> 'nullable|string',
        ], [
            'company_name.required'    => 'Nama Perusahaan wajib diisi.',
            'company_logo.image'       => 'File logo harus berupa gambar.',
            'company_logo.mimes'       => 'Logo harus berformat JPEG, PNG, JPG, atau WEBP.',
            'company_logo.max'         => 'Ukuran file logo maksimal 2MB.',
            'company_stamp.image'      => 'File stempel harus berupa gambar.',
            'company_stamp.mimes'      => 'Stempel harus berformat JPEG, PNG, JPG, atau WEBP.',
            'company_stamp.max'        => 'Ukuran file stempel maksimal 2MB.',
            'company_email.email'      => 'Format email perusahaan tidak valid.',
            'invoice_prefix.required'  => 'Prefix Invoice wajib diisi.',
            'po_prefix.required'       => 'Prefix Purchase Order wajib diisi.',
            'default_tax_rate.numeric' => 'Tarif PPN harus berupa angka (persentase).',
        ]);

        // 2. Ambil record setting
        $setting = Setting::getSetting();

        // 3. Ambil seluruh data teks selain file dan token CSRF
        $data = $request->except(['_token', '_method', 'company_logo', 'company_stamp']);

        // Clean numeric NPWP 16 & NITKU 22
        if (!empty($data['company_npwp16'])) {
            $data['company_npwp16'] = preg_replace('/[^0-9]/', '', $data['company_npwp16']);
        }
        if (!empty($data['company_nitku'])) {
            $data['company_nitku'] = preg_replace('/[^0-9]/', '', $data['company_nitku']);
        } else {
            $data['company_nitku'] = '0000000000000000000000';
        }

        // Handle boolean toggle enable_tax
        $data['enable_tax'] = $request->has('enable_tax');

        // 4. Handle Upload Logo Perusahaan
        if ($request->hasFile('company_logo')) {
            if ($setting->company_logo && Storage::disk('public')->exists($setting->company_logo)) {
                Storage::disk('public')->delete($setting->company_logo);
            }
            $data['company_logo'] = $request->file('company_logo')->store('settings', 'public');
        }

        // 5. Handle Upload Cap Stempel Perusahaan
        if ($request->hasFile('company_stamp')) {
            if ($setting->company_stamp && Storage::disk('public')->exists($setting->company_stamp)) {
                Storage::disk('public')->delete($setting->company_stamp);
            }
            $data['company_stamp'] = $request->file('company_stamp')->store('settings', 'public');
        }

        // 6. Update data ke database
        $setting->update($data);

        return redirect()->back()->with('success', 'Konfigurasi Mini ERP Terpusat Berhasil Diperbarui!');
    }
}