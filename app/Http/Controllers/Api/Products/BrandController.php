<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products\Brand;
use App\Http\Resources\Products\BrandList as BrandResource;

class BrandController extends Controller
{
    public function list(Request $request)
    {
        $list = Brand::orderBy('name','desc')
                    ->when($request->id, function ($query) use ($request) {
                        if($id!=0){
                            $query->where('id', '=',$request->id);
                        }
                    })
                    ->get();

        return response()->json([
            'success' => true,
            'brands' =>  new BrandResource($list)
           ],200);
    }

    public function store(Request $request)
    {
        $brand = Brand::create($request->all());
        return response()->json([
            'success' => true,
            'message' => "Merk Berhasil disimpan"
           ],200);
    }

    public function update(Request $request,$id)
    {
        $brand = Brand::find($id);
        $brand-update($request->all());
        return response()->json([
            'success' => true,
            'message' => "Merk Berhasil diubah"
           ],200);
    }
    public function delete(Request $request,$id)
    {
        $brand = Brand::find($id);
        $brand->delete();
        return response()->json([
            'success' => true,
            'message' =>  "Merk Berhasil dihapus"
           ],200);
    }
}
