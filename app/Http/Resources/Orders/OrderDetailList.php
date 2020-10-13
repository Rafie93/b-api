<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Orders\OrderDetailItem as ItemResource;

class OrderDetailList extends ResourceCollection
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
