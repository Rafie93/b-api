<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductItem extends JsonResource
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
            'id'        => $this->resource->id,
            'order_product_id' => $this->resource->order_product_id,
            'product_id' => $this->resource->product_id,
            'product_name' => $this->resource->product->name,
            'product_sku' => $this->resource->product->sku,
            'quantity_order' => $this->resource->quantity_order,
            'unit'  =>  $this->resource->unit,
            'note_gudang'  =>  $this->resource->note_gudang,
        ];
    }
}
