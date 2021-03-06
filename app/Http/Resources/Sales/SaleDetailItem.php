<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleDetailItem extends JsonResource
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
            'product_id'    => $this->resource->product_id,
            'product_name'    => $this->resource->product->name,
            'variant_id'    => $this->resource->variant_id,
            'quantity'    => $this->resource->quantity,
            'price' => $this->resource->price_sale,
            'type' => $this->resource->type,
            'keterangan'    => $this->resource->keterangan,
            'sub_total' =>  $this->resource->price_sale  *  $this->resource->quantity,
            'thumbnail' => $this->resource->product->thumbnail()

        ];
    }
}
