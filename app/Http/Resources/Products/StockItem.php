<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class StockItem extends JsonResource
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
            'name' => $this->resource->product->name,
            'category' => $this->resource->product->category->name,
            'sku' => $this->resource->product->sku,
            'barcode' => $this->resource->product->barcode,
            'stock'        => $this->resource->stock,
            'unit'      => $this->resource->unit,
            'source' => $this->resource->source
        ];
    }
}
