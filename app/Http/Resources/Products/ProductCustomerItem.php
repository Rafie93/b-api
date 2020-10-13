<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCustomerItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $now = date('Y-m-d H:i:s');
        $is_promo = false;
        $end_promo = $this->resource->end_promotion;
        $start_promo = $this->resource->start_promotion;
        if($start_promo != null && $end_promo!=null){
            if($now >= $start_promo && $now <= $end_promo){
                $is_promo = true;
            }else{
                $is_promo = false;
            }
        }
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
            'price' => $this->resource->price,
            'price_promo' => $this->resource->price_promo,
            'price_member' => $this->resource->price_member,
            'sale_unit' => $this->resource->sale_unit,
            'start_promotion' => $start_promo,
            'end_promotion'=> $end_promo,
            'is_promo'  => $is_promo,
            'is_active' => $this->resource->is_active,
            'thumbnail' => $this->resource->thumbnail(),
            'terjual' => $this->resource->terjual!=null ? $this->resource->terjual :0
        ];
    }
}
