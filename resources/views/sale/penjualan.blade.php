<table>
    <thead>
    <tr>
        <th colspan="7"  style="text-align:center;"><strong>LAPORAN TOTAL PENJUALAN</strong></th>
    <tr>
    <tr>
        <th colspan="3"><strong>Periode : {{ganti_format_tgl_indo($date_start).' s/d '.ganti_format_tgl_indo($date_end)}}</strong></th>
    </tr>
    @if ($kasir!='all')
        <tr><th colspan="3">Kasir : {{$kasir}}</th><tr>
    @endif
</thead>

</table>

<table border="1">
    <thead>
        <tr>
          <th style="text-align:center;width:10px"><strong>No</strong></th>
          @if ($type=="bulanan")
              <th style="text-align:center;width:40px"><strong>Bulan</strong></th>
          @else
              <th style="text-align:center;width:40px"><strong>Tanggal</strong></th>
          @endif
          <th style="text-align:center;width:30px"><strong>Kasir</strong></th>
          <th style="text-align:center;width:30px"><strong>Metode Pembayaran</strong></th>
          <th style="text-align:center;width:30px"><strong>Total</strong></th>
        </tr>
        </thead>
    <tbody>
        @php $no = 1;$grand_total=0;@endphp
        @foreach ($sales as $row)
            <tr>
                <td align="center">{{$no}}</td>
                @if ($type=="bulanan")
                    <td>{{char_to_month($row->month)}}</td>
                @else
                    <td>{{ganti_format_tgl_indo($row->date)}}</td>
                @endif
                <td>{{$row->name}}</td>
                <td align="center">{{$row->payment_channel}}</td>
                <td>{{$row->total_price}}</td>
            </tr>
            @php $no++ ; $grand_total+=$row->total_price; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>

            <td align="center" colspan="4"> Total Uang Masuk</td>
            <td align="right">{{($grand_total)}}</td>
        </tr>
    </tfoot>
</table>

