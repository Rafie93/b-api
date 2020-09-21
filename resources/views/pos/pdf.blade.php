<style id="styles" type="text/css">
    /*Common CSS*/

            .receipt-template .text-small {
                font-size: 8px;
            }
            .receipt-template .block {
                display: block;
            }
            .receipt-template .inline-block {
                display: inline-block;
            }
            .receipt-template .bold {
                font-weight: 700;
            }
            .receipt-template .italic {
                font-style: italic;
            }
            .receipt-template .align-right {
                text-align: right;
            }
            .receipt-template .align-center {
                text-align: center;
            }
            .receipt-template .main-title {
                font-size: 14px;
                font-weight: 700;
                text-align: center;
                margin: 10px 0 5px 0;
                padding: 0;
            }
            .receipt-template .heading {
                position: relation;
            }
            .receipt-template .title {
                font-size: 16px;
                font-weight: 700;
                margin: 10px 0 5px 0;
            }
            .receipt-template .sub-title {
                font-size: 12px;
                font-weight: 700;
                margin: 10px 0 5px 0;
            }
            .receipt-template table {
                width: 100%;
            }
            .receipt-template td,
            .receipt-template th {
                font-size:12px;
            }
            .receipt-template .info-area {
                font-size: 12px;
                line-height: 1.222;
            }
            .receipt-template .listing-area {
                line-height: 1.222;
            }
            .receipt-template .listing-area table {}
            .receipt-template .listing-area table thead tr {
                border-top: 1px solid #000;
                border-bottom: 1px solid #000;
                font-weight: 700;
            }
            .receipt-template .listing-area table tbody tr {
                border-top: 1px dashed #000;
                border-bottom: 1px dashed #000;
            }
            .receipt-template .listing-area table tbody tr:last-child {
                border-bottom: none;
            }
            .receipt-template .listing-area table td {
                vertical-align: top;
            }
            .receipt-template .info-area table {}
            .receipt-template .info-area table thead tr {
                border-top: 1px solid #000;
                border-bottom: 1px solid #000;
            }

    /*Receipt Heading*/
            .receipt-template .receipt-header {
                text-align: center;
            }
            .receipt-template .receipt-header .logo-area {
                width: 80px;
                height: 80px;
                margin: 0 auto;
            }
            .receipt-template .receipt-header .logo-area img.logo {
                display: inline-block;
                max-width: 100%;
                max-height: 100%;
            }
            .receipt-template .receipt-header .address-area {
                margin-bottom: 5px;
                line-height: 1;
            }
            .receipt-template .receipt-header .info {
                font-size: 12px;
            }
            .receipt-template .receipt-header .store-name {
                font-size: 24px;
                font-weight: 700;
                margin: 0;
                padding: 0;
            }


    /*Invoice Info Area*/
        .receipt-template .invoice-info-area {}

    /*Customer Customer Area*/
        .receipt-template .customer-area {
            margin-top:10px;
        }

    /*Calculation Area*/
        .receipt-template .calculation-area {
            border-top: 2px solid #000;
            font-weight: bold;
        }
        .receipt-template .calculation-area table td {
            text-align: right;
        }
        .receipt-template .calculation-area table td:nth-child(2) {
            border-bottom: 1px dashed #000;
        }

    /*Item Listing*/
        .receipt-template .item-list table tr {
            font-family: 'Arial', sans-serif;

        }
        .receipt-template .item-list table td {
            font-family: 'Arial', sans-serif;
             font-size: 10px;
             color:#333333;
        }


/*Barcode Area*/
    .receipt-template .barcode-area {
        margin-top: 10px;
        text-align: center;
    }
    .receipt-template .barcode-area img {
        max-width: 100%;
        display: inline-block;
    }

/*Footer Area*/
    .receipt-template .footer-area {
        line-height: 1.222;
        font-size: 10px;
    }

/*Media Query*/
    @media print {
        .receipt-template {
            width: 100%;
        }
    }
    @media all and (max-width: 215px) {}
</style>

<div class="content-wrapper">

    <!-- Content Start -->
    <section class="content">

      <div class="row">
        <div class="col-xs-12">
          <div class="box box-info">
              <div class='box-body'>

              <div id="invoice" class="row" ng-controller="InvoiceViewController">
                <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">

                    <section class="receipt-template">

                    <header class="receipt-header">
                        {{-- <div class="logo-area">
                            <img class="logo" src="{{public_path('images/logo2.png')}}">
                        </div> --}}
                        <strong>BAHTERA MART</strong>
                        <div class="address-area">

                            <span class="info address">Jl. Trans Kalimantan Km 3,5 Handil Bakti</span>
                            <div class="block">
                                <span class="info phone">Telp:05113307658</span>, <span class="info email">Email: bahteramart.id@gmail.com</span>
                            </div>
                        </div>
                    </header>

                    <section class="info-area">
                        <table>
                            <tr>
                                <td class="w-30"><span>Invoice ID:</td>
                                <td>{{$sale->code}}</td>
                            </tr>
                            <tr>
                                <td class="w-30"><span>Tanggal:</td>
                                <td>{{$sale->date.' '.$sale->time}}</td>
                            </tr>
                            <tr>
                                <td class="w-30">Pelanggan:</td>
                                <td>{{$sale->customerName()}}</td>
                            </tr>
                            <tr>
                                <td class="w-30">Metode Pembayaran:</td>
                                <td>{{$sale->payment_methode}}</td>
                            </tr>

                        </table>
                    </section>

                    <h4 class="main-title">INVOICE</h4>

                    <section class="listing-area item-list">
                        <table >
                            <thead>
                                <tr>
                                    <td class="w-10 text-center"></td>
                                    <td class="w-85 text-center"></td>
                                    <td class="w-15 text-right"></td>
                                    <td class="w-10 text-right"></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail as $det)
                                <tr>
                                    <td align="left" class="text-center">{{$det->quantity}} </td>
                                    <td>{{$det->product->name}}</td>
                                    <td align="right" class="text-right">{{number_format($det->price_sale)}}</td>
                                    <td align="right" class="text-right">{{number_format($det->price_sale * $det->quantity)}}</td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </section>

                    <section class="info-area calculation-area">
                        <table>
                            <tr>
                                <td class="w-70">Sub Total:</td>
                                <td>{{number_format($sale->total_price_product)}}</td>
                            </tr>
                            @if ($sale->total_shipping!=0)
                            <tr>
                                <td class="w-70">Biaya Pengiriman:</td>
                                <td>{{number_format($sale->total_shipping)}}</td>
                            </tr>
                            @endif
                            @if ($sale->discount!=0)
                            <tr>
                                <td class="w-70">Diskon:</td>
                                <td>{{number_format($sale->discount)}}</td>
                            </tr>
                            @endif
                            @if ($sale->total_service!=0)
                            <tr>
                                <td class="w-70">Biaya Layanan:</td>
                                <td>{{number_format($sale->total_service)}}</td>
                            </tr>
                            @endif
                            @if ($sale->total_tax!=0)
                            <tr>
                                <td class="w-70">Pajak:</td>
                                <td>{{number_format($sale->total_tax)}}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="w-70">Grand Total:</td>
                                <td>{{number_format($sale->total_price)}}</td>
                            </tr>
                        </table>
                    </section>

                    </section>
                </div>
              </div>
              </div>
          </div>
        </div>
      </div>
    </section>
</div>

<script>

function uniCharCode(event) {
  var char = event.which || event.keyCode;
  alert(char);
}

function uniKeyCode(event) {
  var key = event.keyCode;
  alert(char);
}
</script>

