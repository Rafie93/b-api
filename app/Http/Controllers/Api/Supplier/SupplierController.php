<?php

namespace App\Http\Controllers\Api\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier\Supplier;
use JWTAuth;
use App\Http\Resources\Supplier\SupplierList as SupplierResource;


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
        $supplier = Supplier::find($id);
        $supplier->delete();
        return response()->json([
            'success' => true,
            'message' =>  "Supplier Berhasil dihapus"
           ],200);
    }
}
