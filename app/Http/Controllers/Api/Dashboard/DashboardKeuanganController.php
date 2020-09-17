<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchases\Purchase;
use App\Models\Orders\Order;
use App\Models\Supplier\Supplier;
use App\Models\Products\SupplierProduct;
use App\Models\Orders\OrderDetail;

class DashboardKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $pesanan_baru_gudang = Order::where('type',2)->where('status',11)->get()->count();
        $po_selesai = Purchase::whereIn('status',[2,5])->get()->count();
        $po_berjalan = Purchase::whereIn('status',[1,4])->get()->count();

        $product = OrderDetail::select('order_product_detail.product_id as id')
                            ->groupBy('order_product_detail.product_id')
                            ->leftJoin('order_product', 'order_product.id', '=', 'order_product_detail.order_product_id')
                            ->where('order_product_detail.status',1)
                            ->where('order_product.type',2)
                            ->get();

        $supplier_product = Supplier::select('supplier_product.supplier_id as id')
                            ->groupBy('supplier_product.supplier_id')
                            ->leftJoin('supplier_product', 'supplier.id', '=', 'supplier_product.supplier_id')
                            ->whereIn('supplier_product.product_id',$product->toArray())
                            ->get();

        $supplier_ready = Supplier::whereIn('id',$supplier_product->toArray())
                        ->get()->count();

        return response()->json(
            [
            'success' => true,
            'dashboard' => array(
                'pesanan_gudang' => $pesanan_baru_gudang,
                'supplier_ready_order' => $supplier_ready,
                'po_berjalan'=> $po_berjalan,
                'po_selesai' => $po_selesai
            )
           ],200);
    }
}
