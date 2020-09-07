<?php

namespace App\Http\Resources\Supplier;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierProductItem extends JsonResource
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
            'supplier_id'        => $this->resource->supplier_id,
            'supplier_name'        => $this->resource->supplier->name,
            'product_id'      => $this->resource->product_id,
            'product_name' => $this->resource->product->name,
            'product_sku' => $this->resource->product->sku,
            'product_barcode' => $this->resource->product->barcode
        ];
    }
}
