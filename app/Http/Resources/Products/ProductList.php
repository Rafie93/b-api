<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Products\ProductItem as ItemResource;
use App\Http\Resources\Products\ProductItemCustomer as ItemResourceCustomer;

class ProductList extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request,$type=null)
    {
        if($type=="customer"){
            return $this->collection->map(function ($item) use ($request) {
                return (new ItemResourceCustomer($item))->toArray($request);
            });
        }else{
            return $this->collection->map(function ($item) use ($request) {
                return (new ItemResource($item))->toArray($request);
            });
        }

    }
}
