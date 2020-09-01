<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use App\Models\Purchases\Purchase;
class PurchaseController extends Controller
{
    //
    public function pdf($id)
    {
        $purchase = Purchase::where('id',$id)->first();
        $pdf = PDF::setOptions(['isRemoteEnabled' => true])
                                ->loadView('purchase.pdf', compact('purchase'))
                                ->setPaper('A4','portrait');
         return $pdf->stream($id.'.e-surat.pdf');
    }
}
