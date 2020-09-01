<?php
namespace App\Models\Purchases;
use App\Models\Products\Product;
use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseDetail;
use Illuminate\Support\Facades\DB;

class PurchaseQuery
{
    public function getProductDetail($id)
    {
        return PurchaseDetail::where('purchase_id',$id)->get();
    }
}
