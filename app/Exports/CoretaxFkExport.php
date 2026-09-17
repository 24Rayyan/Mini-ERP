<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CoretaxFkExport implements Export, WithMultipleSheets
{
    use Exportable;

    protected $documents;
    protected $setting;
    protected $customTaxDate;

    public function __construct($documents, $setting, $customTaxDate = null)
    {
        $this->documents = $documents;
        $this->setting = $setting;
        $this->customTaxDate = $customTaxDate;
    }

    /**
     * Return array of sheets for Coretax DJP
     */
    public function sheets(): array
    {
        return [
            new CoretaxHeaderSheet($this->documents, $this->setting, $this->customTaxDate),
            new CoretaxDetailSheet($this->documents, $this->setting),
        ];
    }
}
