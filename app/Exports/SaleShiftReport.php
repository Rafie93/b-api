<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

use Carbon\Carbon;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Support\Facades\DB;

class SaleShiftReport implements FromView,WithCalculatedFormulas
{
    public function __construct(string $date,string $shift,string $rinci,string $kasir)
    {
        $this->date = $date;
        $this->shift = $shift;
        $this->kasir = $kasir;
        $this->rinci = $rinci;
    }

    public function view(): View
    {
        $date = $this->date;
        $shift = $this->shift;
        $time_start = "15:30:01";
        $time_end = "22:59:59";
        if($shift=='pagi'){
            $time_start = "07:00:00";
            $time_end = "15:30:00";
        }

        $sale = Sale::orderBy('id','desc')
                ->where('date',$date)
                ->whereBetween('time', [$time_start,$time_end])
                ->where(function ($query) {
                    $query->whereNull('status_order')
                        ->orWhere('status_order', '=', 4);
                })
                ->where(function ($query) {
                    if($this->kasir!='all'){
                        $query->where('creator_id',$this->kasir);
                    }
                });

        return view('sale.shift', [
                    'date' => $date,
                    'shift' => $shift,
                    'time_start' => $time_start,
                    'time_end' => $time_end,
                    'kasir' => $this->kasir,
                    'rinci' => $this->rinci,
                    'sales' => $sale->get()
                ]);

    }
}
