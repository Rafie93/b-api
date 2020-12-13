@inject('purchaseQuery', 'App\Models\Purchases\PurchaseQuery')
<table>
    <tr><th colspan="7"  style="text-align:center;"><strong>LAPORAN BARANG MASUK (GUDANG)</strong></th><tr>
    <tr><th colspan="3">Periode : {{$date_start.' s/d '.$date_end}}</th><tr>
</table>

<table border="1">
    <thead>
        <tr>
          <th style="text-align:center;width:10px"><strong>No</strong></th>
          <th style="text-align:center;width:30px"><strong>Tanggal</strong></th>
          <th style="text-align:center;width:50px"><strong>Nama Barang</strong></th>
          <th style="text-align:center;width:20px"><strong>Kode Barang</strong></th>
          <th style="text-align:center;width:10px"><strong>Jumlah</strong></th>
          <th style="text-align:center;width:10px"><strong>Satuan</strong></th>
          <th style="text-align:center;width:15px"><strong>No. Reference</strong></th>
          <th style="text-align:center;width:20px"><strong>Penerima</strong></th>
        </tr>
        </thead>
    <tbody>
        @php $no = 1;@endphp
        @foreach($purchases as $key=>$row)
            @php $detail = $purchaseQuery->getProductDetail($row->id);  @endphp
            @foreach ($detail as $det)
                <tr>
                    <td >{{$no}}</td>
                    <td>{{\Carbon\Carbon::parse($row->date)->format('d M Y')}}</td>
                    <td>{{$det->product->name}}</td>
                    <td>{{$det->product->sku}}</td>
                    <td align="center">{{$det->quantity_received}}</td>
                    <td>{{$det->unit}}</td>
                    <td>{{$row->code}}</td>
                    <td>{{$row->receive()}}</td>
                </tr>
                @php $no++ ; @endphp
            @endforeach
        @endforeach
    </tbody>
</table>

