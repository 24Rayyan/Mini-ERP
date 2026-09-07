<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        // Identitas & Legalitas Usaha
        'company_name',
        'company_tagline',
        'company_npwp',
        'company_npwp16',
        'company_nitku',
        'company_logo',
        'company_stamp',
        'company_email',
        'company_phone',
        'company_website',
        'company_address',
        'company_city',
        'company_bank_account',

        // Penomoran Dokumen
        'po_prefix',
        'po_last_number',
        'invoice_prefix',
        'invoice_last_number',
        'delivery_note_prefix',
        'kwitansi_prefix',

        // Perpajakan & Finansial
        'term_of_payment',
        'default_tax_rate',
        'enable_tax',
        'currency_symbol',
        'currency_code',

        // Otorisasi & Pejabat Penandatangan
        'signatory_name',
        'signatory_position',
        'signatory_city',

        // Catatan Footer & Syarat Ketentuan Standar
        'invoice_footer_notes',
        'receipt_footer_notes',
        'delivery_note_footer_notes',
    ];

    protected $casts = [
        'enable_tax' => 'boolean',
        'default_tax_rate' => 'float',
        'term_of_payment' => 'integer',
    ];

    /**
     * Helper Singleton untuk mengambil atau membuat record setting sistem.
     */
    public static function getSetting(): self
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'company_name' => 'PT Dwitama Cipta Internusa',
                'company_tagline' => 'Business & IT Solutions',
                'company_npwp16' => '0123456789012345',
                'company_nitku' => '0000000000000000000000',
                'company_city' => 'Bandung',
                'invoice_prefix' => 'INV-DCI',
                'po_prefix' => 'PO',
                'delivery_note_prefix' => 'SJ-DCI',
                'kwitansi_prefix' => 'KWT-DCI',
                'default_tax_rate' => 11.00,
                'enable_tax' => true,
                'currency_symbol' => 'Rp',
                'currency_code' => 'IDR',
                'term_of_payment' => 30,
                'signatory_name' => 'Fauzan Septiana',
                'signatory_position' => 'Direktur Utama',
                'signatory_city' => 'Bandung',
            ]);
        }
        return $setting;
    }
}