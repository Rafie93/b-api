<?php

namespace App\Http\Resources\Products;

Use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Products\StockItem as ItemResource;

class StockList extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) use ($request) {
            return (new ItemResource($item))->toArray($request);
        });
    }
}
