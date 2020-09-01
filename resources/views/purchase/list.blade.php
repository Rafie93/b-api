@inject('purchaseQuery', 'App\Models\Purchases\PurchaseQuery')

<table class="receipt-table full-bordered" style="width: 100%">
    <thead>
        <tr>
            <td align="center">NO</td>
            <td align="center">KODE BARANG</td>
            <td align="center">NAMA ITEM</td>
            <td align="center">SATUAN</td>
            <td align="center">QTY</td>
            <td align="center">HARGA SATUAN</td>
            <td align="center">JUMLAH</td>
            <td align="center">KETERANGAN</td>
        </tr>
    </thead>
    <tbody>
        @php
            $rowCount = 1;
            $total = 0;
        @endphp
        @foreach ($purchaseQuery->getProductDetail($purchase->id) as $key=>$row)
            @php
                $subtotal = $row->price * $row->quantity;
            @endphp
        <tr>
            <td align="center">{{$rowCount}}</td>
            <td align="center">{{$row->product->sku}}</td>
            <td align="center">{{$row->product->name}}</td>
            <td align="center">{{$row->unit}}</td>
            <td align="center">{{$row->quantity}}</td>
            <td align="right">Rp. {{number_format($row->price)}}</td>
            <td align="right">Rp. {{number_format($subtotal)}}</td>
            <td>{{$row->notes}}</td>
        </tr>
        @php
            $rowCount += 1;
            $total += $subtotal;
        @endphp
        @endforeach
        @if($rowCount < 26)
            @php
            for ($i=1; $i < 26-$rowCount; $i++) {
                echo "<tr>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";
            }
             @endphp
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">TERBILANG :  {{ucwords(terbilang($total))}}</td>
            <td>TOTAL</td>
            <td align="right">{{number_format($total)}}</td>
            <td></td>
        </tr>

    </tfoot>

</table>
