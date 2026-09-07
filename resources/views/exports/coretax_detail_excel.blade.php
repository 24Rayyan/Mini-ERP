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
            @endphp
            @foreach($doc->items as $idx => $item)
                @php
                    $itemDpp = $item->subtotal;
                    $itemPpn = $itemDpp * ($taxRatePercent / 100);
                @endphp
                <tr>
                    <td style="mso-number-format:'\@';">{{ $doc->document_number }}</td>
                    <td>{{ $idx + 1 }}</td>
                    <td style="mso-number-format:'\@';">ITEM-{{ str_pad($idx + 1, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->unit ?? 'Pcs' }}</td>
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
