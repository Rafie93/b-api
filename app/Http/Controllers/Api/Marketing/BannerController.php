<?php

namespace App\Http\Controllers\Api\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion\Banner;
use App\Http\Resources\Banners\BannerList as BannerResource;
use File;

class BannerController extends Controller
{
    public function list(Request $request)
    {
        return response()->json([
            'success'=>true,
            'banners'=>new BannerResource(Banner::all())
        ], 200);
    }
    public function store(Request $request)
    {
          if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);

            $banner = Banner::create([
                'title'=>$someRequest['title'],
                'description'=>$someRequest['description'],
                'creator_id' => auth()->user()->id
            ]);
            $foto = $request->file('file');
            $fileName = $foto->getClientOriginalName();
            $request->file('file')->move('images/banner/'.$banner->id,$fileName);
            $fotoUpdate = Banner::where('id',$banner->id)
                                  ->update(['image' => $fileName]);
            return response()->json([
            'success'=>true,
            'message'=>'Banner Berhasil disimpan'
             ], 200);

          }else{
            return response()->json([
                'success'=>false,
                'message'=>'Tidak ada image yang diupload'
            ], 400);
          }
    }
    public function update(Request $request,$id)
    {
        $banner = Banner::where('id',$id)->first();
        if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);
            $banner->update([
                'title'=>$someRequest['title'],
                'description'=>$someRequest['description'],
                'creator_id' => auth()->user()->id
            ]);
            $foto = $request->file('file');
            $fileName = $foto->getClientOriginalName();
            $request->file('file')->move('images/banner/'.$banner->id,$fileName);
            $fotoUpdate = Banner::where('id',$banner->id)
                                  ->update(['image' => $fileName]);

        }else{
            $banner->update([
                'title'=>$request->title,
                'description'=>$request->description,
                'creator_id' => auth()->user()->id
            ]);
        }

        return response()->json([
            'success'=>true,
            'message'=>'Berhasil di update'
        ], 200);
    }
    public function delete(Request $request,$id)
    {
        $banner = Banner::find($id);
        $banner->delete();
        $path = 'images/banner/'.$id;
        File::deleteDirectory(public_path($path));

        return response()->json([
            'success' => true,
            'message' =>  "Banner Berhasil dihapus"
           ],200);
    }
}
