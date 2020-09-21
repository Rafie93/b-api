<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;

class StrukController extends Controller
{
    public function pdf($id)
    {

        $sale = Sale::where('code',$id)->first();
        $detail = SaleDetail::where('sale_id',$sale->id)->get();

        $hight = 300;
        for ($i=0; $i < count($detail); $i++) {
           $hight += 20;
        }

        $customPaper = array(0,0,226.77, $hight);

        $pdf = PDF::setOptions(['isRemoteEnabled' => true])
                                ->loadView('pos.pdf', compact('sale','detail'))
                                ->setPaper($customPaper);
        return $pdf->stream($id.'.invoice.pdf');
    }
}
