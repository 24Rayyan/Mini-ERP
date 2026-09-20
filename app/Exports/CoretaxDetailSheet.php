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

class CoretaxDetailSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle, WithColumnFormatting
{
    protected $documents;
    protected $setting;

    public function __construct($documents, $setting)
    {
        $this->documents = $documents;
        $this->setting = $setting;
    }

    public function view(): View
    {
        return view('exports.coretax_detail_excel', [
            'documents' => $this->documents,
            'setting'   => $this->setting,
        ]);
    }

    public function title(): string
    {
        return 'DetailFaktur';
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,               // 1. Baris
            'B' => NumberFormat::FORMAT_TEXT,                 // 2. Barang/Jasa (A/B)
            'C' => NumberFormat::FORMAT_TEXT,                 // 3. Kode Barang Jasa
            'D' => NumberFormat::FORMAT_TEXT,                 // 4. Nama Barang/Jasa
            'E' => NumberFormat::FORMAT_TEXT,                 // 5. Nama Satuan Ukur
            'F' => '#,##0.00',                                // 6. Harga Satuan
            'G' => NumberFormat::FORMAT_NUMBER,               // 7. Jumlah Barang Jasa
            'H' => '#,##0.00',                                // 8. Total Diskon
            'I' => '#,##0.00',                                // 9. DPP
            'J' => '#,##0.00',                                // 10. DPP Nilai Lain
            'K' => NumberFormat::FORMAT_NUMBER,               // 11. Tarif PPN
            'L' => '#,##0.00',                                // 12. PPN
            'M' => NumberFormat::FORMAT_NUMBER,               // 13. Tarif PPnBM
            'N' => '#,##0.00',                                // 14. PPnBM
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F172A']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}