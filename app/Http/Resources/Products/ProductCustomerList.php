<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Products\ProductCustomerItem as ItemResource;

class ProductCustomerList extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request,$type=null)
    {
        return $this->collection->map(function ($item) use ($request) {
            return (new ItemResource($item))->toArray($request);
        });
    }
}
