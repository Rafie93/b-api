<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class PromoItem extends JsonResource
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
            'id'    => $this->resource->id,
            'product_id' => $this->resource->product_id,
            'product_name' => $this->resource->product->name,
            'quantity' => $this->resource->quantity,
        ];
    }
}
