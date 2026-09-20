<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoretaxHeaderSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle, WithColumnFormatting
{
    protected $documents;
    protected $setting;
    protected $customTaxDate;

    public function __construct($documents, $setting, $customTaxDate = null)
    {
        $this->documents = $documents;
        $this->setting = $setting;
        $this->customTaxDate = $customTaxDate;
    }

    public function view(): View
    {
        return view('exports.coretax_header_excel', [
            'documents'     => $this->documents,
            'setting'       => $this->setting,
            'customTaxDate' => $this->customTaxDate,
        ]);
    }

    public function title(): string
    {
        return 'Faktur';
    }

    public function columnFormats(): array
    {
        // Sesuaikan urutan kolom dengan Blade View
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Baris
            'B' => NumberFormat::FORMAT_TEXT, // Tanggal Faktur
            'D' => NumberFormat::FORMAT_TEXT, // Kode Transaksi
            'H' => NumberFormat::FORMAT_TEXT, // Referensi
            'J' => NumberFormat::FORMAT_TEXT, // ID TKU Penjual (22 digit)
            'K' => NumberFormat::FORMAT_TEXT, // NPWP/NIK Pembeli (16 digit)
            'N' => NumberFormat::FORMAT_TEXT, // Nomor Dokumen Pembeli
            'R' => NumberFormat::FORMAT_TEXT, // ID TKU Pembeli (22 digit)
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            // Baris 3 adalah Header Kolom Utama
            3 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E40AF']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}