<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        html {
                margin: 10px 13px 0 13px;
            }

            body {
                font-family: 'Arial', sans-serif;
                font-size:10px;
            }

            table.main-table {
                width: 100%;
                border-collapse: collapse;
            }

            table.labels-table {
                font-size:12px;
                width: 100%;
                border-collapse: collapse;
            }

            table.labels-table td {
                border-spacing: 2px;
                padding: 0px 5px;
                border: 1px solid #666;
            }

            table.labels-table th {
                border: 1px solid #666;
                padding: 5px 0;
            }

            table.mini-receipt-table {
                font-size:10px;
                width: 100%;
                border-collapse: collapse;
            }

            table.mini-receipt-table td {
                padding: 2px 3px;
            }

            table.mini-receipt-table th {
                padding: 2px 0;
            }

            table.header-table {
                font-size:12px;
                width: 750px;
                border-collapse: collapse;
            }

            table.header-table th {
                padding: 2px 0px;
            }



            h1, h2, h3, h4 {
                margin: 0;
            }

            .text-center {
                text-align: center;
                vertical-align:middle;
            }

            .text-right {
                text-align: right;
            }

            .text-left {
                text-align: left;
            }

            table .text-top {
                vertical-align: top;
            }

            table .text-middle {
                vertical-align: middle;
            }

            table .text-bottom {
                vertical-align: bottom;
            }

            .bam-print-logo {
                width: 70px;
            }

            table {
                border-spacing: 1px;
            }

            td, th {
                padding: 0;
            }

            td {
                vertical-align: top;
            }

            p {
                margin: 0 0 3px 0;
            }

            div {
                margin: 0;
                padding: 0;
            }

            .lead {
                font-size: 12px;
            }

            .strong { font-weight: bold; }

            .border-bottom {
                border-bottom: 1px solid #000;
                clear: both;
            }

            .page-break {
                page-break-after: always;
            }

            table.receipt-table, table.receipt-table table {
                /*width: 100%;*/
                border-collapse: collapse;
                font-size: 10px;
            }

            table.receipt-table th {
                background-color: #ccc;
                font-size: 10px;
            }

            table.receipt-table th, table.receipt-table td {
                height: 12px;
            }

            table.full-bordered th, table.full-bordered td {
                border: 1px solid #aaa;
                padding: 3px;
            }
            .vertical-center {
                margin: 0;
                position: absolute;
                top: 50%;
                -ms-transform: translateY(-50%);
                transform: translateY(-50%);
            }

    </style>
    <title>No.Surat - {{$purchase->code }}</title>
    {{-- <link rel="stylesheet" href="{{asset('assets/css/pdf/pdf.css')}}" /> --}}
</head>
<body>

    <div>
        <table class="receipt-table" style="width: 100%">
            <tbody>
                <tr>
                    <td align="center">
                        <img src="{{public_path('images/logo2.png')}}" height="80">
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <br> <h1>PURCHASE ORDER  (F.04)</h1>
                        <br><br><br>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table   style="width: 50%">
                            <tr>
                                <td>TANGGAL </td>
                                <td>:
                                     {{Carbon\Carbon::parse($purchase->date)->format('D, d M Y')}}
                                </td>

                            </tr>
                            <tr>
                                <td>WAKTU </td>
                                <td>:
                                    {{Carbon\Carbon::parse($purchase->date)->format('H:i:s')}}
                                </td>
                            </tr>
                            <tr>
                                <td>NO. PO </td>
                                <td>: {{$purchase->code}} </td>
                            </tr>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td align="center"><br>
                       @include('purchase.list')
                    </td>
                </tr>
            </tbody>
        </table><br><br>
        <table class="recipt-table" style="width: 100%">
            <tbody>
                <tr>
                    <td align="center">PENERIMA ORDER</td>
                    <td align="center">PEMOHON</td>
                    <td align="center">PENANGGUNG JAWAB</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 70px"></td>
                </tr>

                <tr>
                    <td  align="center">{{$purchase->receive()}}</td>
                    <td  align="center">{{$purchase->creator()}}</td>
                    <td  align="center">{{$purchase->approved()}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
