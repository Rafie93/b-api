<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseDetail;

class ProductInGudangReport implements  FromView,WithCalculatedFormulas
{
    public function __construct(string $date_start,string $date_end)
    {
        $this->date_start = $date_start;
        $this->date_end = $date_end;
    }

    public function view(): View
    {
        $date_start = $this->date_start;
        $date_end = $this->date_end;

        $date_start = replaceDate($date_start);
        $e = explode(" ",$date_start);
        $d_s = $e[0];
        $t_s = $e[1];

        $date_end = replaceDate($date_end);
        $ed = explode(" ",$date_end);
        $d_e = $ed[0];
        $t_e = $ed[1];

        $purchase = Purchase::orderBy('id','asc')
                    ->whereNotNull('receive_date')
                    ->whereBetween('date', [$d_s,$d_e]);

        return view('purchase.productin', [
            'date_start' => $d_s,
            'date_end' => $d_e,
            'purchases' => $purchase->get()
        ]);

    }
}
