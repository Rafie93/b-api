<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Products\Product;
class GambarItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $productId=$this->resource->product_id;
        $thumbnail = Product::where('id',$productId)->first()->thumbnail;
        return  [
            'id'    => $this->resource->id,
            'product_id' => $productId,
            'image' => $this->resource->image(),
            'thumbnail' => $thumbnail==$this->resource->image ? 'Ya' : 'Tidak'
        ];
    }
}
