<table>
    <thead>
    <tr>
        <th colspan="7"  style="text-align:center;"><strong>LAPORAN JUMLAH BARANG TERJUAL</strong></th>
    <tr>
    <tr>
        <th colspan="3"><strong>Periode : {{ganti_format_tgl_indo($date_start).' s/d '.ganti_format_tgl_indo($date_end)}}</strong></th>
    </tr>

</thead>

</table>

<table border="1">
    <thead>
        <tr>
          <th style="text-align:center;width:10px"><strong>No</strong></th>
          @if ($type!='all')
                @if ($type=='mingguan')
                    <th style="text-align:center;width:20px"><strong>Minggu Ke</strong></th>
                @elseif ($type=='bulanan')
                <th style="text-align:center;width:20px"><strong>Bulan</strong></th>
                 @else
                    <th style="text-align:center;width:20px"><strong>Tanggal</strong></th>
                @endif

          @endif
          <th style="text-align:center;width:60px"><strong>Nama Produk</strong></th>
          <th style="text-align:center;width:30px"><strong>Kode Produk</strong></th>
          <th style="text-align:center;width:20px"><strong>Barcode Produk</strong></th>
          <th style="text-align:center;width:20px"><strong>Jumlah Jual</strong></th>
        </tr>
        </thead>
    <tbody>
        @php $no = 1;$grand_total=0;@endphp
        @foreach ($product_sold as $row)
            <tr>
                <td align="center">{{$no}}</td>
                @if ($type!='all')
                    @if ($type=='mingguan')
                        <td align="center">{{$row->week}}</td>
                    @elseif ($type=='bulanan')
                        <td align="center">{{char_to_month($row->month)}}</td>
                    @else
                        <td>{{$row->date}}</td>
                    @endif
                @endif
                <td>{{$row->name}}</td>
                <td>{{$row->sku}}</td>
                <td>{{$row->barcode}}</td>
                <td align="center">{{$row->count}}</td>
            </tr>
            @php $no++ ; $grand_total+=$row->count; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            @php $cols = '4'; @endphp
            @if ($type!='all')
             @php $cols = '5'; @endphp
            @endif
            <td align="center" colspan="{{$cols}}"> Total Terjual</td>
            <td align="center">{{($grand_total)}}</td>
        </tr>
    </tfoot>
</table>

