<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products\Category;
use App\Http\Resources\Products\CategoryList as CategoryResource;

class CategoryController extends Controller
{
    public function list_parent(Request $request)
    {
        $parentCategory = Category::whereNull('parent_id')->get();
        return response()->json([
            'success' => true,
            'categories' =>  new CategoryResource($parentCategory)
           ],200);
    }

    public function list_kategori(Request $request,$id=0)
    {
        $listCategory = Category::whereNotNull('parent_id')
                                    ->when($id, function ($query) use ($id) {
                                        if($id!=0){
                                            $query->where('parent_id', '=',$id);
                                        }
                                    })
                                    ->when($request->keyword, function ($query) use ($request) {
                                        $query->where('name', 'LIKE','%'.$request->parent_id.'%');
                                    })
                                    ->get();
        return response()->json([
            'success' => true,
            'categories' =>  new CategoryResource($listCategory)
           ],200);
    }

    public function store(Request $request)
    {
        $category = Category::create($request->all());
        if ($request->hasFile('image')) {
            $foto = $request->file('image');
            $fileName = $foto->getClientOriginalName();
            $request->file('image')->move('images/category/'.$category->id,$fileName);
            $fotoUpdate = Category::where('id',$category->id)->update(['image' => $fileName]);
       }
       return response()->json([
        'success'=>true,
        'message'=> "Kategori Sukses disimpan"
        ], 200);

    }

    public function update(Request $request,$id)
    {
        $category = Category::find($id);
        $category->update($request->all());
        if ($request->hasFile('image')) {
            $foto = $request->file('image');
            $fileName = $foto->getClientOriginalName();
            $request->file('image')->move('images/category/'.$category->id,$fileName);
            $fotoUpdate = Category::where('id',$category->id)->update(['image' => $fileName]);
       }
       return response()->json([
        'success'=>true,
        'message'=> "Kategori Sukses diubah"
        ], 200);
    }

    public function delete($id)
    {
        $category = Category::find($id);
        $isDelete = $category->isDelete();
        if($isDelete){
            $category->delete();
            return response()->json([
                'success'=>true,
                'message'=> "Data berhasil dihapus"
            ], 200);
        }else{
            return response()->json([
                'success'=>false,
                'message'=> "Data tidak bisa dihapus, karena sedang dipakai"
            ], 400);
        }
    }
}
