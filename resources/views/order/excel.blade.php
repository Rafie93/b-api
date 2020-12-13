<table>
    <thead>
    <tr><th colspan="5"  style="text-align:center;"><strong>LAPORAN BARANG KELUAR</strong></th><tr>
    <tr><th colspan="3">Periode : {{$date_start.' s/d '.$date_end}}</th><tr>
    <tr><th colspan="3">Dari : Gudang</th><tr>
    </thead>
</table>

<table border="1">
    <thead>
    <tr>
        <th style="text-align:center;width:10px"><strong>No</strong></th>
        <th style="text-align:center;width:20px"><strong>Tanggal</strong></th>
        <th style="text-align:center;width:40px"><strong>Nama Barang</strong></th>
        <th style="text-align:center;width:20px"><strong>Kode Barang</strong></th>
        <th style="text-align:center;width:10px"><strong>Jumlah</strong></th>
        <th style="text-align:center;width:20px"><strong>Ref. No </strong></th>
    </tr>
    </thead>
    <tbody>
        @php $no=1; @endphp
        @foreach($orders as $key=>$row)
        <tr>
            <td align="center">{{$no}}</td>
            <td>{{$row->date}}</td>
            <td>{{$row->name}}</td>
            <td>{{$row->sku}}</td>
            <td align="center">{{($row->quantity_received)}}</td>
            <td>{{$row->code}}</td>
        </tr>
        @php $no++; @endphp

        @endforeach
    </tbody>
</table>
