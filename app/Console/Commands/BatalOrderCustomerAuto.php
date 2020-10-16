<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Support\Facades\DB;
use App\Models\Products\Product;
use App\Models\Products\ProductStock;
use App\Models\Products\ProductStockHistory;
use Carbon\Carbon;
use App\User;

class BatalOrderCustomerAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:batal-order-customer-auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command batal order customer auto';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
      $sales =  Sale::where('status_order',1)
                    ->whereNotNull('date_order')
                    ->whereNull('date_payment')
                    ->get();

      $now = date('Y-m-d H:i:s');
      foreach ($sales as $row) {
        $saleId = $row->id;
        $code = $row->code;
        $creatorId = $row->creator_id;
        $date_order = $row->date_order;
        $date_jatuh_tempo = Carbon::parse($date_order);
        $date_jatuh_tempo2 = $date_jatuh_tempo->addHours(24)->format('Y-m-d H:i:s');
        if($now > $date_jatuh_tempo2){
            try
            {
                DB::beginTransaction();
                    $update = Sale::where('id',$saleId)->update([
                        'date_cancel'=> date('Y-m-d H:i:s'),
                        'status_order' => 99,
                    ]);

                    $detail = SaleDetail::where('sale_id',$saleId)->get();
                    foreach ($detail as $det){
                        $det_id = $det->id;
                        SaleDetail::where('id',$det_id)->update([
                            'status'=>2
                        ]);

                        $products_data = Product::where('id',$det->product_id)->first();
                        $unit = $products_data->sale_unit;
                        $this->penambahan_stock($det->product_id,$det->quantity,$unit,$code);


                    }

                DB::commit();
                $user = User::where('id',$creatorId)->first();
                if($user->fcm_token!=null){
                    $judul = "Hai ".$user->name;
                    $isi = "No. Pesanan Anda ".$code." Telah dibatalkan oleh sistem kami";
                    sendMessageToDevice($judul,
                                        $isi,
                                        $user->fcm_token);
                }


            }  catch (\PDOException $e) {
                DB::rollBack();

            }
        }
      }
    }

    public function penambahan_stock($product_id,$quantity,$unit,$code)
    {
        $ps = ProductStock::where('product_id',$product_id)
                    ->where('source',1)
                    ->where('unit',$unit)
                    ->first();
        $stock_sekarang = $ps->stock;
        $ps->update([ 'stock' => $stock_sekarang + $quantity]);
        ProductStockHistory::insert([
            'date' => date('Y-m-d H:i:s'),
            'product_id' => $product_id,
            'quantity' => $quantity,
            'unit' => $unit,
            'source' => 1,
            'ref_code' => $code
        ]);
    }

}
