@inject('purchaseQuery', 'App\Models\Purchases\PurchaseQuery')

<table>
    <tr><th colspan="7"  style="text-align:center;"><strong>LAPORAN PEMBELIAN</strong></th><tr>
    <tr><th colspan="3">Periode : {{$date_start.' s/d '.$date_end}}</th><tr>

</table>

<table border="1">
    <thead>
    <tr>
      <th style="text-align:center;width:10px"><strong>No</strong></th>
      <th style="text-align:center;width:30px"><strong>Tanggal</strong></th>
      <th style="text-align:center;width:30px"><strong>No. Pembelian</strong></th>
      <th style="text-align:center;width:30px"><strong>Pemohon</strong></th>
      <th style="text-align:center;width:30px"><strong>Penerima</strong></th>
      <th style="text-align:center;width:30px"><strong>Ke Supplier</strong></th>
      <th style="text-align:center;width:30px"><strong>Total</strong></th>
    </tr>

    </thead>
    <tbody>
        @php $no=1 ; $grand_total=0;@endphp
        @foreach($purchases as $key=>$row)
        <tr>
            <td align="center">{{$no}}</td>
            <td>{{\Carbon\Carbon::parse($row->date)->format('d M Y')}}</td>
            <td>{{$row->code}}</td>
            <td>{{$row->creator()}}</td>
            <td>{{$row->receive()}}</td>
            <td>{{$row->supplier()}}</td>
            <td  align="right">{{($row->grand_total)}}</td>
        </tr>
        @if ($rinci == 'ya')
            <tr><td colspan="6"></td></tr>
            @php $detail = $purchaseQuery->getProductDetail($row->id);  @endphp
            @foreach ($detail as $det)
                <tr>
                    <td colspan="2"></td>
                    <td style="background-color:#74ddaa">{{$det->product->name}}</td>
                    <td style="background-color:#74ddaa">{{$det->price}}</td>
                    <td style="background-color:#74ddaa">{{$det->quantity_received}}</td>
                    <td style="background-color:#74ddaa">{{$det->unit}}</td>
                    <td style="background-color:#74ddaa">{{$det->quantity_received * $det->price}}</td>
                </tr>
            @endforeach
            <tr><td colspan="6"></td></tr>
        @endif
        @php $no++ ;$grand_total+=$row->grand_total; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>

            <td align="center" colspan="6"> Total Pembelian</td>
            <td align="right">{{($grand_total)}}</td>
        </tr>
    </tfoot>
</table>

