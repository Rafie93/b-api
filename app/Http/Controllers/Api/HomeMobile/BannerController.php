<?php

namespace App\Http\Controllers\Api\HomeMobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion\Banner;
use App\Http\Resources\Banners\BannerList as BannerResource;

class BannerController extends Controller
{
    public function getBanner(Request $request)
    {
        $banner = Banner::all();
        return response()->json([
            'success'=>true,
            'banner'=>new BannerResource($banner)
        ], 200);
    }


}
