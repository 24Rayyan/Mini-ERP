<table>
    <thead>
        <tr>
            <th>KODE_TRANSAKSI</th>
            <th>NOMOR_DOKUMEN_REFERENSI</th>
            <th>TANGGAL_FAKTUR</th>
            <th>NPWP_PENJUAL</th>
            <th>NITKU_PENJUAL</th>
            <th>NAMA_PENJUAL</th>
            <th>ALAMAT_PENJUAL</th>
            <th>JENIS_ID_PEMBELI</th>
            <th>NOMOR_ID_PEMBELI</th>
            <th>NITKU_PEMBELI</th>
            <th>NAMA_PEMBELI</th>
            <th>ALAMAT_PEMBELI</th>
            <th>JUMLAH_DPP</th>
            <th>JUMLAH_PPN</th>
            <th>JUMLAH_PPNBM</th>
            <th>KETERANGAN_TAMBAHAN</th>
            <th>NAMA_PENANDATANGAN</th>
            <th>JABATAN_PENANDATANGAN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($documents as $doc)
            @php
                // Kalkulasi Finansial
                $bruto = $doc->items->sum('subtotal');
                $potongan = $doc->discount ?? 0;
                $dpp = max(0, $bruto - $potongan);
                // Prioritas Kode FP: (1) Detail Customer, (2) Kode di Dokumen, (3) Default 040
                $customerFpCode = $doc->customer?->default_tax_transaction_code ?? null;
                $rawTrxCode = (!empty($customerFpCode)) ? $customerFpCode : ($doc->tax_transaction_code ?? '040');
                if (empty($rawTrxCode)) $rawTrxCode = '040';
                $trxCode = substr(preg_replace('/[^0-9]/', '', $rawTrxCode), 0, 2);
                if (empty($trxCode)) $trxCode = '04';
                $otherTaxBase = ($trxCode === '04') ? round(($dpp * 11) / 12, 2) : $dpp;
                $taxRatePercent = ($setting->enable_tax ?? true) ? ($setting->default_tax_rate ?? 11) : 0;
                $ppn = ($trxCode === '04') ? round($otherTaxBase * ($taxRatePercent / 100), 2) : round($dpp * ($taxRatePercent / 100), 2);
                // Kode FP 3 digit untuk output kolom (e.g. '040', '020')
                $fpCodeOutput = str_pad($trxCode, 3, '0', STR_PAD_RIGHT);

                // ID Type Mapping for Coretax DJP
                $idType = 'TIN';
                if ($doc->customer?->tax_id_type == 'NIK') {
                    $idType = 'NATIONAL_ID';
                } elseif ($doc->customer?->tax_id_type == 'PASPOR') {
                    $idType = 'PASSPORT';
                }

                // Tanggal Faktur
                $taxDate = !empty($customTaxDate) 
                    ? date('Y-m-d', strtotime($customTaxDate)) 
                    : (!empty($doc->tax_invoice_date) ? date('Y-m-d', strtotime($doc->tax_invoice_date)) : date('Y-m-d', strtotime($doc->date)));

                // NPWP & NITKU formatting (Clean numeric string)
                $sellerNpwp = preg_replace('/[^0-9]/', '', $setting->company_npwp16 ?? $setting->company_npwp ?? '0123456789012345');
                $sellerNitku = preg_replace('/[^0-9]/', '', $setting->company_nitku ?? '0000000000000000000000');
                
                $buyerId = preg_replace('/[^0-9a-zA-Z]/', '', $doc->customer?->tax_id_number ?? $doc->customer?->phone ?? '0000000000000000');
                $buyerNitku = preg_replace('/[^0-9]/', '', $doc->customer?->nitku ?? '0000000000000000000000');
            @endphp
            <tr>
                <td style="mso-number-format:'\@';">{{ $fpCodeOutput }}</td>
                <td style="mso-number-format:'\@';">{{ $doc->document_number }}</td>
                <td>{{ $taxDate }}</td>
                <td style="mso-number-format:'\@';">{{ str_pad($sellerNpwp, 16, '0', STR_PAD_LEFT) }}</td>
                <td style="mso-number-format:'\@';">{{ str_pad($sellerNitku, 22, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $setting->company_name ?? 'PT DWITAMA CIPTA INTERNUSA' }}</td>
                <td>{{ $setting->company_address ?? 'Bandung' }}</td>
                <td>{{ $idType }}</td>
                <td style="mso-number-format:'\@';">{{ $buyerId }}</td>
                <td style="mso-number-format:'\@';">{{ str_pad($buyerNitku, 22, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $doc->customer?->name }}</td>
                <td>{{ $doc->customer?->address ?? '-' }}</td>
                <td>{{ $trxCode === '04' ? $otherTaxBase : $dpp }}</td>
                <td>{{ $ppn }}</td>
                <td>0</td>
                <td>{{ $doc->payment_note ?? 'Invoice Ref: ' . $doc->document_number }}</td>
                <td>{{ $setting->signatory_name ?? 'Fauzan Septiana' }}</td>
                <td>{{ $setting->signatory_position ?? 'Direktur Utama' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
