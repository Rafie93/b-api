@inject('pQuery', 'App\Models\Products\ProductQuery')
<table>
    <tr><th colspan="7"  style="text-align:center;"><strong>LAPORAN HISTORY STOCK
        @if ($source=='1')
            {{'STORE'}}
        @else
            {{'GUDANG'}}
        @endif</strong>
    </th><tr>

</table>

<table border="1">
    <thead>
        <tr>
          <th style="text-align:center;width:10px"><strong>No</strong></th>
          <th style="text-align:center;width:50px"><strong>Nama Barang</strong></th>
          <th style="text-align:center;width:30px"><strong>Kode Barang</strong></th>
          <th style="text-align:center;width:30px"><strong>Satuan</strong></th>
          <th style="text-align:center;width:30px"><strong>Stock Sekarang</strong></th>

        </tr>
        </thead>
    <tbody>
        @php $no = 1;@endphp
        @foreach ($stock as $row)
            <tr>
                <td >{{$no}}</td>
                <td>{{$row->product->name}}</td>
                <td>{{$row->product->sku}}</td>
                <td>{{$row->unit}}</td>
                <td align="center">{{$row->stock}}</td>
            </tr>
            @php
                $history = $pQuery->history_product($source,$row->product_id);
            @endphp
            @foreach ($history as $his)
                <tr>
                    <td colspan="2"></td>
                    <td  style="background-color:#74ddaa">{{$his->ref_code}}</td>
                    <td  style="background-color:#74ddaa">{{$his->quantity}}</td>
                </tr>
            @endforeach

          @php $no++ ; @endphp
        @endforeach
    </tbody>
</table>

