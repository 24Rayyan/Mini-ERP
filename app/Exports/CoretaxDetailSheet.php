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
        return 'DETAIL_OBJEK_FAKTUR';
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // NOMOR DOKUMEN REFERENSI
            'B' => NumberFormat::FORMAT_NUMBER, // NO URUT BARIS
            'C' => NumberFormat::FORMAT_TEXT, // KODE BARANG JASA
            'D' => NumberFormat::FORMAT_TEXT, // NAMA BARANG JASA
            'E' => NumberFormat::FORMAT_TEXT, // SATUAN
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // HARGA SATUAN
            'G' => NumberFormat::FORMAT_NUMBER, // QTY
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // TOTAL DPP
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // DISKON
            'J' => NumberFormat::FORMAT_PERCENTAGE_00, // TARIF PPN
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // NOMINAL PPN
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // NOMINAL PPNBM
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
