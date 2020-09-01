<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sales;
class SalesController extends Controller
{
    public function index(Request $request)
    {
        $list = Sales::orderBy('id','desc')
                                    ->when($request->status, function ($query) use ($request) {
                                        $query->where('status', '=',$request->status);

                                    })
                                    ->when($request->keyword, function ($query) use ($request) {
                                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                                    })
                                    ->get();
        return response()->json([
            'success' => true,
            'sales' => $list
           ],200);
    }
}
