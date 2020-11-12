@inject('orderQuery', 'App\Models\Orders\OrderQuery')

<table class="receipt-table full-bordered" style="width: 100%">
    <thead>
        <tr>
            <td align="center">NO</td>
            <td align="center">KODE BARANG</td>
            <td align="center">NAMA ITEM</td>
            <td align="center">SATUAN</td>
            <td align="center">QTY</td>
            <td align="center">KETERANGAN</td>
        </tr>
    </thead>
    <tbody>
        @php
            $rowCount = 1
        @endphp
        @foreach ($orderQuery->getProductDetail($order->id) as $key=>$row)
        <tr>
            <td align="center">{{$rowCount}}</td>
            <td align="center">{{$row->product->sku}}</td>
            <td align="center">{{$row->product->name}}</td>
            <td align="center">{{$row->unit}}</td>
            <td align="center">{{$row->quantity_order}}</td>
            <td>{{$row->notes}}</td>
        </tr>
        @php
            $rowCount += 1
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
                echo "</tr>";
            }
             @endphp
        @endif
    </tbody>

</table>
