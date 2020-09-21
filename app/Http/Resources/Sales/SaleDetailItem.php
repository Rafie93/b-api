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
            'quantity'    => $this->resource->quantity,
            'price' => $this->resource->price_sale,
            'type' => $this->resource->type,
            'sub_total' =>  $this->resource->price_sale  *  $this->resource->quantity
        ];
    }
}
