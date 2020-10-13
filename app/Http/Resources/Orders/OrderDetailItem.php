<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'id'      => $this->resource->id,
            'product_id'     => $this->resource->product_id,
            'product_name' => $this->resource->products->name,
            'product_price_id' =>  $this->resource->product_price_id,
            'keterangan' => $this->resource->keterangan,
            'thumbnail' => $this->resource->products->thumbnail(),
            'price'      => $this->resource->price,
            'quantity'      => $this->resource->quantity,
        ];
    }
}
