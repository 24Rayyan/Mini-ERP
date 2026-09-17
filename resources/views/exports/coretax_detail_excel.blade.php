<table>
    <thead>
        <tr>
            <th>NOMOR_DOKUMEN_REFERENSI</th>
            <th>BARIS</th>
            <th>KODE_BARANG_JASA</th>
            <th>NAMA_BARANG_JASA</th>
            <th>SATUAN</th>
            <th>HARGA_SATUAN</th>
            <th>KUANTITAS</th>
            <th>TOTAL_HARGA_DPP</th>
            <th>DISKON</th>
            <th>TARIF_PPN</th>
            <th>NOMINAL_PPN</th>
            <th>TARIF_PPNBM</th>
            <th>NOMINAL_PPNBM</th>
        </tr>
    </thead>
    <tbody>
        @foreach($documents as $doc)
            @php
                $taxRatePercent = ($setting->enable_tax ?? true) ? ($setting->default_tax_rate ?? 11) : 0;
                // Prioritas Kode FP: (1) Detail Customer, (2) Kode di Dokumen, (3) Default 040
                $customerFpCode = $doc->customer?->default_tax_transaction_code ?? null;
                $rawTrxCode = (!empty($customerFpCode)) ? $customerFpCode : ($doc->tax_transaction_code ?? '040');
                if (empty($rawTrxCode)) $rawTrxCode = '040';
                $trxCode = substr(preg_replace('/[^0-9]/', '', $rawTrxCode), 0, 2);
                if (empty($trxCode)) $trxCode = '04';
            @endphp
            @foreach($doc->items as $idx => $item)
                @php
                    $itemDpp = $item->subtotal;
                    $otherTaxBase = ($trxCode === '04') ? (isset($item->other_tax_base) ? (float)$item->other_tax_base : round(($itemDpp * 11) / 12, 2)) : 0;
                    $taxBaseForVat = ($trxCode === '04') ? $otherTaxBase : $itemDpp;
                    $itemPpn = round($taxBaseForVat * ($taxRatePercent / 100), 2);
                    $opt = \App\Services\CoretaxXmlService::determineItemOpt($item);
                    $unitCode = \App\Services\CoretaxXmlService::getUnitCode($opt);
                    $itemCode = $item->coretax_code ?? '000000';
                @endphp
                <tr>
                    <td style="mso-number-format:'\@';">{{ $doc->document_number }}</td>
                    <td>{{ $idx + 1 }}</td>
                    <td style="mso-number-format:'\@';">{{ $itemCode }}</td>
                    <td>{{ $item->description }}</td>
                    <td style="mso-number-format:'\@';">{{ $unitCode }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $itemDpp }}</td>
                    <td>0</td>
                    <td>{{ $taxRatePercent / 100 }}</td>
                    <td>{{ $itemPpn }}</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
