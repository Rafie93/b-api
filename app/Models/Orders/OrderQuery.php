<?php
namespace App\Models\Orders;
use App\Models\Products\Product;
use App\Models\Orders\Order;
use App\Models\Orders\OrderDetail;
use Illuminate\Support\Facades\DB;

class OrderQuery
{
    public function getProductDetail($id)
    {
        return OrderDetail::where('order_product_id',$id)->get();
    }
    public function getProductDetailDiterima($id)
    {
        return OrderDetail::where('order_product_id',$id)->where('status',5)->get();
    }
}
