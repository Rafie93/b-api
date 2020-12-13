@inject('saleQuery', 'App\Models\Sales\SaleQuery')
<table>
        <tr>
            <th colspan="5"  style="text-align:center;"><strong>LAPORAN PENJUALAN PER SHIFT ({{$shift}})</strong></th>
        <tr>
        <tr><th colspan="3">Tanggal : {{$date}}</th><tr>
        <tr><th colspan="3">Waktu : {{$time_start.' - '.$time_end}}</th><tr>
        @if ($kasir!='all')
            <tr><th colspan="3">Kasir : {{$kasir}}</th><tr>
        @endif

</table>

<table border="1">
    <thead>

    <tr>
        @php
            $cols_add=0;
        @endphp
      <th style="text-align:center;width:10px"><strong>No</strong></th>
      <th style="text-align:center;width:30px"><strong>Tanggal</strong></th>
      <th style="text-align:center;width:20px"><strong>Time</strong></th>
        @if ($kasir=='all')
            @php $cols_add+=1; @endphp
            <th style="text-align:center;width:20px"><strong>Kasir</strong></th>@endif
        <th style="text-align:center;width:30px"><strong>Metode Pembayaran</strong></th>
        <th style="text-align:center;width:30px"><strong>No. Order</strong></th>
        <th style="text-align:center;width:30px"><strong>Total Penjualan</strong></th>
    </tr>

    </thead>
    <tbody>
        @php $no=1 ; $grand_total=0;@endphp
        @foreach($sales as $key=>$row)
        <tr>
            <td align="center">{{$no}}</td>
            <td>{{ganti_format_tgl_indo($row->date)}}</td>
            <td>{{$row->time}}</td>
            @if ($kasir=='all')<td>{{$row->creator->name}}</td>@endif
            <td>{{$row->payment_channel}}</td>
            <td>{{$row->code}}</td>
            <td  align="right">{{($row->total_price)}}</td>
        </tr>
        @if ($rinci == 'ya')
        <tr><td colspan="6"></td></tr>
        @php $detail = $saleQuery->getDetail($row->id);@endphp
        @foreach ($detail as $det)
            <tr>
                <td ></td>
                <td style="background-color:#74ddaa">{{$det->product->name}}</td>
                <td style="background-color:#74ddaa">{{$det->quantity}} * </td>
                <td style="background-color:#74ddaa">{{$det->price_sale}}</td>
                <td style="background-color:#74ddaa">{{$det->quantity * $det->price_sale}}</td>
            </tr>
        @endforeach
        <tr><td colspan="6"></td></tr>
        @endif

        @php $no++ ;$grand_total+=$row->total_price; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            @php
                $total_colspan = 5 + $cols_add;
            @endphp
            <td align="right" colspan="{{$total_colspan}}"> Total Penjualan</td>
            <td align="right">{{($grand_total)}}</td>
        </tr>
    </tfoot>
</table>

