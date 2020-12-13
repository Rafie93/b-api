<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

use Carbon\Carbon;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Support\Facades\DB;

class SaleReport implements FromView,WithCalculatedFormulas
{
    public function __construct(string $date_start,string $date_end,string $payment_method,string $kasir,string $rinci)
    {
        $this->date_start = $date_start;
        $this->date_end = $date_end;
        $this->payment_method = $payment_method;
        $this->kasir = $kasir;
        $this->rinci = $rinci;
    }

    public function view(): View
    {
        $date_start = $this->date_start;
        $date_end = $this->date_end;


        $sale = Sale::orderBy('id','desc')
                ->where(function ($query) {
                    $query->whereNull('status_order')
                        ->orWhere('status_order', '=', 4);
                });

        if($this->payment_method != 'all'){
            $sale = $sale->where('payment_methode', '=',$this->payment_method);
        }
        if($this->kasir != 'all'){
            $sale = $sale->where('creator_id', '=',$this->kasir);
        }
        $date_start = replaceDate($date_start);
        $e = explode(" ",$date_start);
        $d_s = $e[0];
        $t_s = $e[1];

        $date_end = replaceDate($date_end);
        $ed = explode(" ",$date_end);
        $d_e = $ed[0];
        $t_e = $ed[1];
        $sale = $sale->whereBetween('date', [$d_s,$d_e]);

        return view('sale.excel', [
            'date_start' => $date_start,
            'date_end' => $date_end,
            'payment' => $this->payment_method,
            'kasir' => $this->kasir,
            'rinci' => $this->rinci,
            'sales' => $sale->get()
        ]);

    }
}
