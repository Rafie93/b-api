<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sales\Sale;
use Carbon\Carbon;
use App\User;

class KonfirmPaymentCustomerAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:konfirm-auto-success-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command jika customer sudah melakukan transfer pembayaran tetapi belum di konfirmasi oleh admin';

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
        $sales =  Sale::where('status',1)
                        ->where('payment_methode','Transfer')
                        ->whereNull('date_payment_confirmation')
                        ->whereNotNull('date_payment')
                        ->whereNotNull('image')->get();
        $now = date('Y-m-d H:i:s');
        foreach ($sales as $row) {
            $saleId = $row->id;
            $code = $row->code;
            $creatorId = $row->creator_id;
            $date_payment = $row->date_payment;
            $date_lama_auto_konfirm = Carbon::parse($date_payment);
            $date_maksimal_wajib_konfirm = $date_lama_auto_konfirm->addHours(24)->format('Y-m-d H:i:s');
            if($now > $date_maksimal_wajib_konfirm){
              $sale =  Sale::where('id',$saleId)->update([
                    'status'=>1,
                    'date_payment_confirmation' => $now
                ]);
                $user = User::where('id',$creatorId)->first();
                if($user->fcm_token!=null){
                    $judul = "Hai ".$user->name;
                    $isi = "Pembayaran dari No Pesanan ".$code." Telah Kami terima, harap tunggu kami akan proses pengirimannya";
                    sendMessageToDevice($judul,
                                        $isi,
                                        $user->fcm_token);
                }
            }
        }
    }
}
