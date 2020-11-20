<?php

namespace App\Http\Controllers\Api\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier\Supplier;
use App\Models\Products\SupplierProduct;
use App\Models\Orders\Order;
use App\Models\Orders\OrderDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Supplier\SupplierList as SupplierResource;

class PurchaseSupplierControoler extends Controller
{
    public function supplier_order(Request $request)
    {
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

        $supplier = Supplier::whereIn('id',$supplier_product->toArray())
                             ->get();

        return response()->json([
            'success' => true,
            'suppliers' =>new SupplierResource($supplier)
            ],200);
    }

    public function supplier_product_order(Request $request,$supplier_id)
    {
        $product = DB::table('order_product_detail')
                        ->select('order_product_detail.product_id',
                            'product.sku','product.barcode','product.name','product.price','product.price_modal',
                            'order_product_detail.unit',
                            DB::raw('SUM(order_product_detail.quantity_order) as quantity_order'))
                        ->groupBy('order_product_detail.product_id','order_product_detail.unit',
                        'product.sku','product.barcode','product.name','product.price','product.price_modal')
                        ->leftJoin('order_product', 'order_product.id', '=', 'order_product_detail.order_product_id')
                        ->leftJoin('product', 'product.id', '=', 'order_product_detail.product_id')
                        ->leftJoin('supplier_product', 'product.id', '=', 'supplier_product.product_id')
                        ->where('order_product_detail.status',1)
                        ->where('order_product.type',2)
                        ->where('product.product_type',1)
                        ->where('supplier_product.supplier_id',$supplier_id)
                        ->orderBy('order_product_detail.product_id','asc')
                        ->get();

        return response()->json([
            'success' => true,
            'orders' => $product
        ],200);
    }

    public function supplier_product_by_supplier(Request $request,$supplier_id)
    {
        $product = SupplierProduct::select('supplier_product.supplier_id','supplier.name as supplier_name','supplier_product.product_id as product_id',
                                    'product.name as product_name','product.sale_unit',
                                    'product.sku','product.barcode','product.price_modal'
                                    )
                                    ->leftJoin('product','product.id','=','supplier_product.product_id')
                                    ->leftJoin('supplier','supplier.id','=','supplier_product.supplier_id')
                                    ->where('supplier_product.supplier_id',$supplier_id)
                                    ->get();
        $output = [];
        foreach ($product as $row){
            $jumlah_kebutuhan = $this->quantity_order($row->product_id);
            if($jumlah_kebutuhan >0){
                $output[]= array(
                    'supplier_id' => $row->supplier_id,
                    'supplier_name' => $row->supplier_name,
                    'product_id' => $row->product_id,
                    'product_name' => $row->product_name,
                    'product_sku' => $row->sku,
                    'product_barcode' => $row->barcode,
                    'price' => $row->price_modal,
                    'unit' => $row->sale_unit,
                    'jumlah_kebutuhan' => $jumlah_kebutuhan
                );
            }

        }
        return response()->json([
            'success' => true,
            'products' => $output
        ],200);
    }
    public function quantity_order($product_id)
    {
        $det = OrderDetail::select('quantity_order')
                    ->raw('SUM(quantity_order) as quantity_order')
                    ->where('product_id',$product_id)
                    ->where('status',1)
                    ->first();
        if($det){
            return $det->quantity_order;
        }else{
            return 0;
        }

    }

}
