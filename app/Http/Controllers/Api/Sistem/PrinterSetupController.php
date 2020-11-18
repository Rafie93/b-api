<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Sistem\SettingPrinter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class PrinterSetupController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    public function index(Request $request)
    {
        $printers = SettingPrinter::where('user_id',$this->user->id)->get();
        return response()->json([
            'success'=>true,
            'message' =>'Sukses',
            'saved' => $printers->count() > 0 ? true : false,
            'printers' => $printers->first()
          ], 200);
    }

    public function create_printer(Request $request)
    {
        $dataStore = [
            'user_id'      => $this->user->id,
            'type'          => $request->type_printer==null ? 'USB' : $request->type_printer,
            'ip_address'           => $request->ip_address,
            'port'            => $request->port,
            'app_key' => $request->app_key,
            'app_port' => $request->app_port,
            'printer_name' => $request->printer_name,
            'created_at'       => date('Y-m-d H:i:s')
        ];
        DB::table('setting_printer')->updateOrInsert([
            'user_id' => $this->user->id
        ],$dataStore);

        return response()->json([
         'success'=>true,
         'message' =>  "Pengaturan Printer Berhasil di atur"
       ], 200);
    }

    public function struk(Request $request, $code)
    {
        $sale = Sale::where('code',$code)->first();
        $detail = SaleDetail::where('sale_id',$sale->id)->get();
        $printer = SettingPrinter::where('user_id',$this->user->id)->first();
        $ip = $printer->ip_address; // IP Komputer kita atau printer lain yang masih satu jaringan
        $printer = $printer->printer_name; // Nama Printer yang di sharing

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
