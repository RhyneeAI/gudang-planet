{{-- resources/views/reports/sales-revenue.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Omset Penjualan</title>
    <style>
        * { margin: 15px; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }

        .header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #333; }
        .header h2 { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #555; }

        .warning-box { background-color: #FFF3CD; border-left: 4px solid #FFC107; padding: 8px 12px; margin: 0 0 16px 0; font-size: 10px; color: #856404; border-radius: 4px; }

        .section-title { font-size: 13px; font-weight: bold; margin: 16px 0 8px; padding: 4px 8px; background-color: #f0f0f0; border-left: 4px solid #333; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; margin-left: -1.75px; }
        thead tr { background-color: #333; color: #fff; }
        thead th { padding: 6px 8px; text-align: left; font-size: 10px; text-align: center; }
        tbody tr:nth-child(even) { background-color: #f9f9f9; }
        tbody td { padding: 5px 8px; font-size: 10px; border-bottom: 1px solid #eee; }
        td.number { text-align: right; }
        td.center { text-align: center; }

        tbody tr.rank-1-soft { background-color: #FFF9C4; } /* Kuning lembut */
        tbody tr.rank-2-soft { background-color: #FFF3E0; } /* Oranye lembut */
        tbody tr.rank-3-soft { background-color: #F5F5F5; } /* Abu-abu lembut */

        .summary { margin-top: 16px; padding: 10px; background-color: #333; color: #fff; text-align: right; font-size: 11px; }
        .summary span { margin-left: 24px; }

        .footer { margin-top: 16px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Omset Penjualan</h2>
        <p>Periode: {{ $period['from'] }} s/d {{ $period['to'] }}</p>
    </div>

    <div class="warning-box">
        <strong>Informasi Penting :</strong> <br><br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Harga jual pada laporan ini adalah harga saat transaksi dilakukan, 
        Perubahan harga pada master data tidak akan mempengaruhi data historis penjualan untuk menjaga keaslian laporan.
    </div>

    {{-- Tabel Atas: Top 10 Produk Terlaris --}}
    <div class="section-title">Top 10 Produk Terlaris</div>
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th class="number">Harga Jual</th>
                <th class="number">Qty Terjual</th>
                <th class="number">Omset</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($top_products as $index => $item)
            @php
                $rankClass = '';
                if ($index == 0) $rankClass = 'rank-1-soft';
                elseif ($index == 1) $rankClass = 'rank-2-soft';
                elseif ($index == 2) $rankClass = 'rank-3-soft';
            @endphp
            <tr class="{{ $rankClass }}">
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $item['code'] }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="number">Rp {{ number_format($item['sell_price'], 0, ',', '.') }}</td>
                <td class="number">{{ number_format($item['qty_sold'], 0, ',', '.') }}</td>
                <td class="number">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tabel Bawah: Detail Semua Transaksi --}}
    <div class="section-title">Detail Penjualan Per Transaksi</div>
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal Transaksi</th>
                <th>Kasir</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th class="number">Harga Jual</th>
                <th class="number">Qty</th>
                <th class="number">Omset</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details as $transaction)
                @php $itemCount = count($transaction['items']); @endphp
                
                @foreach ($transaction['items'] as $itemIndex => $item)
                    <tr>
                        @if ($itemIndex === 0)
                            {{-- Baris pertama: tampilkan info transaksi dengan rowspan --}}
                            <td class="center" rowspan="{{ $itemCount }}">{{ $loop->parent->iteration }}</td>
                            <td rowspan="{{ $itemCount }}">{{ $transaction['transaction_code'] }}</td>
                            <td rowspan="{{ $itemCount }}">{{ $transaction['date'] }}</td>
                            <td rowspan="{{ $itemCount }}">{{ $transaction['cashier'] }}</td>
                        @endif
                        
                        {{-- Detail produk (selalu tampil) --}}
                        <td>{{ $item['code'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="number">Rp {{ number_format($item['sell_price'], 0, ',', '.') }}</td>
                        <td class="number">{{ number_format($item['quantity'], 0, ',', '.') }}</td>
                        <td class="number">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        TOTAL OMSET KESELURUHAN
        <span>Total Qty : {{ number_format($grand_total['total_qty'], 0, ',', '.') }}</span>
        <span>Total Omset : Rp {{ number_format($grand_total['total_revenue'], 0, ',', '.') }}</span>
    </div>

    <div class="footer">
        Digenerate pada : {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>