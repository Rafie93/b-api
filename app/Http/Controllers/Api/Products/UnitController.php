<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Products\Unit;
use App\Http\Resources\Products\UnitList as UnitResource;

class UnitController extends Controller
{
    public function list(Request $request)
    {
        $list = Unit::orderBy('code','desc')
                    ->when($request->id, function ($query) use ($request) {
                        if($id!=0){
                            $query->where('id', '=',$request->id);
                        }
                    })
                    ->get();

        return response()->json([
            'success' => true,
            'unit' =>  new UnitResource($list)
           ],200);
    }

    public function store(Request $request)
    {
        $unit = Unit::create($request->all());
        return response()->json([
            'success' => true,
            'message' =>  "Unit Berhasil ditambahkan"
           ],200);
    }

    public function update(Request $request,$id)
    {
        $unit = Unit::find($id);
        $unit->update($request->all());
        return response()->json([
            'success' => true,
            'message' =>  "Unit Berhasil diubah"
           ],200);
    }
    public function delete(Request $request,$id)
    {
        $unit = Unit::find($id);
        $unit->delete();
        return response()->json([
            'success' => true,
            'message' =>  "Unit Berhasil dihapus"
           ],200);
    }
}
