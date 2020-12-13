<table>
    <tr><th colspan="7"  style="text-align:center;"><strong>LAPORAN DATA STOCK
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
          <th style="text-align:center;width:40px"><strong>Nama Barang</strong></th>
          <th style="text-align:center;width:30px"><strong>Kode Barang</strong></th>
          <th style="text-align:center;width:30px"><strong>Stock</strong></th>
          <th style="text-align:center;width:30px"><strong>Satuan</strong></th>
        </tr>
        </thead>
    <tbody>
        @php $no = 1;@endphp
        @foreach ($stock as $row)
            <tr>
                <td >{{$no}}</td>
                <td>{{$row->product->name}}</td>
                <td>{{$row->product->sku}}</td>
                <td align="center">{{$row->stock}}</td>
                <td>{{$row->unit}}</td>
            </tr>
            @php $no++ ; @endphp
        @endforeach
    </tbody>
</table>

