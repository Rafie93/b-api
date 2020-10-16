<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sales\Sale;
use Carbon\Carbon;

class KonfirmOrderCustomerAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:konfirm-auto-terima';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command konfirmasi otomatis jika barang dikirim dan diterima tetapi belum di konfirmasi oleh user';

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
        $sales =  Sale::where('status_order',3)
                    ->whereNull('date_complete')
                    ->whereNotNull('date_shipping')
                    ->get();
        $now = date('Y-m-d H:i:s');
        foreach ($sales as $row) {
            $saleId = $row->id;
            $code = $row->code;
            $date_shipping = $row->date_shipping;
            $date_lama_auto_konfirm = Carbon::parse($date_shipping);
            $date_maksimal_wajib_konfirm = $date_lama_auto_konfirm->addHours(48)->format('Y-m-d H:i:s');
            if($now > $date_maksimal_wajib_konfirm){
                Sale::where('id',$saleId)->update([
                    'status_order'=>4,
                    'date_complete' => $now
                ]);
            }
        }
    }
}
