<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class StrukController extends Controller
{
    public function pdf($code)
    {

        $sale = Sale::where('code',$code)->first();
        $detail = SaleDetail::where('sale_id',$sale->id)->get();

        $hight = 300;
        for ($i=0; $i < count($detail); $i++) {
           $hight += 20;
        }

        $customPaper = array(0,0,226.77, $hight);

        $pdf = PDF::setOptions(['isRemoteEnabled' => true])
                                ->loadView('pos.pdf', compact('sale','detail'))
                                ->setPaper($customPaper);
        return $pdf->stream($code.'.invoice.pdf');
    }
    public function struk(Request $request, $code)
    {
        $sale = Sale::where('code',$code)->first();
        $detail = SaleDetail::where('sale_id',$sale->id)->get();

        $ip = '192.168.43.225'; // IP Komputer kita atau printer lain yang masih satu jaringan
        $printer = 'EPSON TM-U220 Receipt'; // Nama Printer yang di sharing
        $connector = new WindowsPrintConnector("smb://" . $ip . "/" . $printer);
        $printer = new Printer($connector);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("BAHTERA MART \n");
        $printer->text("Jl.Trans Kalimantan Km 3,5 Handil Bakti\n");
        $printer->text("Telp:05113307658 \n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Order ID :" . $sale->code . "\n");
        $printer->text("Tanggal  :" . $sale->date . "\n");
        $printer->text("Metode   :" . $sale->payment_methode . "\n");
        $printer->setPrintLeftMargin(0);
        $printer->text("----------------------------------------");
        foreach ($detail as $det) {
            $printer->setPrintLeftMargin(256);
            $subTotal = number_format($det->price_sale * $det->quantity);
            $priceProduk = number_format($det->price_sale);
            $textLeft = $det->quantity.' x '.$det->product->name;
            $maks = 39;
            $countRight = strlen($priceProduk.' '.$subTotal);
            $countLeft = strlen($textLeft.' ');
            $total = $countRight+$countLeft;
            if($total<$maks){
                for($i=0; $i<$maks-$total; $i++){
                    $textLeft .= '.';
                }
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text($textLeft.' '.$priceProduk.' '.$subTotal. "\n");
            }else if ($total > $maks){
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text($textLeft. "\n");
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text($priceProduk.' '.$subTotal. "\n");
            }

        }
        $printer->setPrintLeftMargin(0);
        $printer->text("----------------------------------------");
        $printer->text("\n");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text("Sub Total : " . number_format($sale->total_price_product) . "\n");
        if($sale->total_shipping!=0){
            $printer->text("Biaya Pengiriman : " . number_format($sale->total_shipping) . "\n");
        }
        if($sale->diskon!=0){
            $printer->text("Diskon : " . number_format($sale->diskon) . "\n");
        }
        if($sale->total_service!=0){
            $printer->text("Biaya Layanan : " . number_format($sale->total_service) . "\n");
        }
        if($sale->total_tax!=0){
            $printer->text("Pajak : " . number_format($sale->total_tax) . "\n");
        }
        $printer->text("Grand Total : " . number_format($sale->total_price) . "\n");
        $printer->text("\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("*** Terima Kasih atas kunjungannya ** \n");
        $printer->cut();
        $printer->close();

    }
}
