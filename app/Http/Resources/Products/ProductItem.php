<?php

namespace App\Http\Resources\Products;

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
            'product_type' => $this->resource->product_type,
            'sku' => $this->resource->sku,
            'name'      => $this->resource->name,
            'category_id'   => $this->resource->category_id,
            'category_label'      => $this->resource->category->name,
            'category_parent' => $this->resource->category_parent(),
            'barcode' => $this->resource->barcode,
            'brand' => $this->resource->brand,
            'description' => $this->resource->description,
            'price_modal' => $this->resource->price_modal,
            'price' => $this->resource->price,
            'alert_quantity' => $this->resource->alert_quantity,
            'alert_quantity_warehouse' => $this->resource->alert_quantity_warehouse,
            'is_active' => $this->resource->is_active,
            'thumbnail' => $this->resource->thumbnail(),
        ];
    }
}
