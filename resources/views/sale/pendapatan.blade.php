@inject('saleQuery', 'App\Models\Sales\SaleQuery')
<table>
    <tr><th colspan="5"  style="text-align:center;"><strong>LAPORAN PENDAPATAN</strong></th><tr>
    <tr><th colspan="3">Periode : {{$date_start.' s/d '.$date_end}}</th><tr>
    @if ($payment!='all')
         <tr><th colspan="3">Metode Pembayaran : {{$payment}}</th><tr>
    @endif
    @if ($kasir!='all')
        <tr><th colspan="3">Kasir : {{$kasir}}</th><tr>
    @endif
</table>

<table border="1">
    <thead>

    <tr>
        @php $cols_add=0;@endphp
        <th style="text-align:center;width:10px"><strong>No</strong></th>
        <th style="text-align:center;width:30px"><strong>Tanggal</strong></th>
        <th style="text-align:center;width:20px"><strong>Time</strong></th>
        @if ($kasir=='all')
            @php $cols_add+=1; @endphp
            <th style="text-align:center;width:20px"><strong>Kasir</strong></th>
        @endif
        @if ($payment=='all') @php $cols_add+=1; @endphp <th style="text-align:center;width:30px"><strong>Metode Pembayaran</strong></th>@endif
        @if ($payment!='all' && $payment!='Tunai')
            @php $cols_add+=1; @endphp
            <th style="text-align:center;width:30px"><strong>Jenis Pembayaran</strong></th>
        @endif
        <th style="text-align:center;width:30px"><strong>No. Order</strong></th>
        <th style="text-align:center;width:30px"><strong>Total Penjualan</strong></th>
    </tr>

    </thead>
    <tbody>
        @php $no=1 ; $grand_total=0;$total_product=0; @endphp
        @foreach($sales as $key=>$row)
        @php
            $detail = $saleQuery->getDetail($row->id);
            $countDetail = $detail->count();
            $total_product += $saleQuery->sumTotalProduct($row->id);
        @endphp
        <tr>
            <td align="center">{{$no}}</td>
            <td>{{ganti_format_tgl_indo($row->date)}}</td>
            <td>{{$row->time}}</td>
            @if ($kasir=='all')<td>{{$row->creator->name}}</td>@endif
            @if ($payment=='all')
             <td>{{$row->payment_methode}}
                @if ($row->payment_methode!='Tunai')
                   <br> {{$row->payment_channel}}
                @endif
             </td>
            @endif
            @if ($payment!='all' && $payment!='Tunai')  <td>{{$row->payment_channel}}</td> @endif
            <td>{{$row->code}}</td>
            <td  align="right">{{($row->total_price)}}</td>
        </tr>
        <tr colspan="9"></tr>
        <tr>
            <td></td>
            <td style="background-color:#12a25d">Nama Barang</td>
            <td style="background-color:#12a25d">Harga Modal</td>
            <td style="background-color:#12a25d">Harga Jual</td>
            <td style="background-color:#12a25d">Quantity</td>
            <td style="background-color:#12a25d">Total Modal</td>
            <td style="background-color:#12a25d">Total Jual</td>
        </tr>
            @foreach ($detail as $key => $det)
                <tr>
                    <td></td>
                    <td style="background-color:#74ddaa">{{$det->product->name}}</td>
                    <td style="background-color:#74ddaa">{{$det->price_product}}</td>
                    <td style="background-color:#74ddaa">{{$det->price_sale}}</td>
                    <td style="background-color:#74ddaa">{{$det->quantity}}</td>
                    <td style="background-color:#74ddaa">{{$det->quantity * $det->price_product}}</td>
                    <td style="background-color:#74ddaa">{{$det->price_sale * $det->quantity}}</td>
                </tr>
            @endforeach
        <tr colspan="9"></tr>

        @php $no++ ;$grand_total+=$row->total_price; @endphp
        @endforeach
    </tbody>
    <tfoot>
        @php
            $total_colspan = 4 + $cols_add;
        @endphp
        <tr>
            <td align="right" colspan="{{$total_colspan}}"><strong> Total Penjualan </strong></td>
            <td align="right"><strong>{{number_format($grand_total)}}</strong></td>
        </tr>
        <tr>
            <td align="right" colspan="{{$total_colspan}}"><strong> Total Modal</strong></td>
            <td align="right"><strong>{{number_format($total_product)}}</strong></td>
        </tr>
        <tr>
            <td align="right" colspan="{{$total_colspan}}"><strong> Total Bersih </strong></td>
            <td align="right"><strong>{{number_format($grand_total - $total_product)}}</strong></td>
        </tr>
    </tfoot>
</table>

