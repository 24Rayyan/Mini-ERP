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
        return 'FAKTUR_KELUARAN';
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // KODE TRANSAKSI
            'B' => NumberFormat::FORMAT_TEXT, // NOMOR DOKUMEN REFERENSI
            'C' => NumberFormat::FORMAT_TEXT, // TANGGAL FAKTUR
            'D' => NumberFormat::FORMAT_TEXT, // NPWP PENJUAL (16 DIGIT)
            'E' => NumberFormat::FORMAT_TEXT, // NITKU PENJUAL (22 DIGIT)
            'F' => NumberFormat::FORMAT_TEXT, // NAMA PENJUAL
            'G' => NumberFormat::FORMAT_TEXT, // ALAMAT PENJUAL
            'H' => NumberFormat::FORMAT_TEXT, // JENIS ID PEMBELI
            'I' => NumberFormat::FORMAT_TEXT, // NOMOR ID PEMBELI (16 DIGIT)
            'J' => NumberFormat::FORMAT_TEXT, // NITKU PEMBELI (22 DIGIT)
            'K' => NumberFormat::FORMAT_TEXT, // NAMA PEMBELI
            'L' => NumberFormat::FORMAT_TEXT, // ALAMAT PEMBELI
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // DPP
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // PPN
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // PPNBM
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E40AF']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
