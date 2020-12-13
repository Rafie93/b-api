<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Support\Facades\DB;

class PendapatanTotalReport implements  FromView,WithCalculatedFormulas
{

    public function __construct(string $date_start,string $date_end,string $type, string $kasir)
    {
        $this->date_start = $date_start;
        $this->date_end = $date_end;
        $this->kasir = $kasir;
        $this->type = $type;

    }

    public function view(): View
    {
        $date_start = $this->date_start;
        $date_end = $this->date_end;
        $kasir = $this->kasir;
        $type = $this->type;
        $date_start = replaceDate($date_start);
        $e = explode(" ",$date_start);
        $d_s = $e[0];
        $t_s = $e[1];

        $date_end = replaceDate($date_end);
        $ed = explode(" ",$date_end);
        $d_e = $ed[0];
        $t_e = $ed[1];

        if($type=='harian' || $type =='all'){
            $sale = DB::table('sale')
            ->leftjoin('users', 'users.id', '=', 'sale.creator_id')
            ->select('date','creator_id','users.name','payment_channel', DB::raw('SUM(total_price) as total_price'))
            ->where('status',1)
            ->whereBetween('date', [$d_s,$d_e])
            ->where(function ($query) {
                $query->whereNull('status_order')
                    ->orWhere('status_order', '=', 4);
            })
            ->where(function ($query) {
                if($this->kasir!='all'){
                    $query->where('creator_id',$kasir);
                 }
            })
            ->groupBy('date','creator_id','users.name','payment_channel');

        }else if($type=='bulanan'){
            $sale = DB::table('sale')
            ->leftjoin('users', 'users.id', '=', 'sale.creator_id')
            ->select( DB::Raw('MONTH(sale.date) month'),'creator_id','users.name','payment_channel', DB::raw('SUM(total_price) as total_price'))
            ->where('status',1)
            ->whereBetween('date', [$d_s,$d_e])
            ->where(function ($query) {
                $query->whereNull('status_order')
                    ->orWhere('status_order', '=', 4);
            })
            ->where(function ($query) {
                if($this->kasir!='all'){
                    $query->where('creator_id',$kasir);
                 }
            })
            ->groupBy('month','creator_id','users.name','payment_channel');
        }


        return view('sale.penjualan', [
            'date_start' => $d_s,
            'date_end' => $d_e,
            'kasir' => $kasir,
            'type' => $type,
            'sales' => $sale->get()
        ]);
    }
}
