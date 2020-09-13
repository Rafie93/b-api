<?php

namespace App\Http\Controllers\Api\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier\Supplier;
use App\Models\Products\SupplierProduct;
use App\Models\Purchases\Purchase;
use JWTAuth;
use App\Http\Resources\Supplier\SupplierList as SupplierResource;
use App\Http\Resources\Supplier\SupplierItem as SupplierItem;
use App\Http\Resources\Supplier\SupplierProductList as SupplierProductResoure;


class SupplierController extends Controller
{
    public function index(Request $request)
    {
          $list = Supplier::orderBy('name','asc')
                    ->get();

        return response()->json([
            'success' => true,
            'suppliers' =>  new SupplierResource($list)
           ],200);
    }
    public function store(Request $request)
    {
        $supplier = Supplier::create($request->all());
        return response()->json([
            'success' => true,
            'message' =>  "Supplier Berhasil ditambahkan"
           ],200);
    }

    public function detail($id)
    {
       $supplier = Supplier::find($id);
       $supplier_product = SupplierProduct::where('supplier_id',$id)->get();
       return response()->json([
        'success' => true,
        'suppliers' =>  new SupplierItem($supplier),
        'supplier_product' => new SupplierProductResoure($supplier_product)
       ],200);

    }

    public function update(Request $request,$id)
    {
        $supplier = Supplier::find($id);
        $supplier->update($request->all());
        return response()->json([
            'success' => true,
            'message' =>  "Supplier Berhasil diubah"
           ],200);
    }

    public function delete(Request $request,$id)
    {
        $sp = SupplierProduct::where('supplier_id',$id)->get()->count();
        $pc = Purchase::where('supplier_id',$id)->get()->count();
        if(($pc<=0) && ($sp<=0)){
            $supplier = Supplier::find($id);
            $supplier->delete();
            return response()->json([
                'success' => true,
                'message' =>  "Supplier Berhasil dihapus"
               ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' =>  "Supplier Tidak dapat dihapus"
               ],400);
        }

    }
}
