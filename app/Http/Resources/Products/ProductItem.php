<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Products\ProductImage;
use App\Models\Products\ProductStock;
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
        $stock_store = ProductStock::where('product_id',$this->resource->id)->where('source',1)->get();
        $stock_gudang = ProductStock::where('product_id',$this->resource->id)->where('source',2)->get();
        $now = date('Y-m-d H:i:s');
        $is_promo = 'false';
        $end_promo = $this->resource->end_promotion;
        $start_promo = $this->resource->start_promotion;
        if($start_promo != null && $end_promo!=null){
            if($now >= $start_promo && $now <= $end_promo){
                $is_promo = 'true';
            }else{
                $is_promo = 'false';
                $start_promo = null;
                $end_promo = null;
            }
        }
        return  [
            'id'        => $this->resource->id,
            'value'        => $this->resource->id,
            'label'        => $this->resource->name,
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
            'price_promo' => $this->resource->price_promo,
            'converse_unit' => $this->resource->converse_unit,
            'alert_quantity' => $this->resource->alert_quantity,
            'alert_quantity_warehouse' => $this->resource->alert_quantity_warehouse,
            'is_active' => $this->resource->is_active,
            'thumbnail' => $this->resource->thumbnail(),
            'total_image' => $this->resource->image_product->count(),
            'total_supplier' =>$this->resource->supplier->count(). " Supplier",
            'stock' =>$stock_store->count() != 0 ? $stock_store->first()->stock : 0,
            'stock_gudang' =>$stock_gudang->count() != 0 ? $stock_gudang->first()->stock : 0,
            'is_promo' => $is_promo,
            'mulai_promosi' => $start_promo,
            'selesai_promosi' => $end_promo,
            'price_type' => $this->resource->price_type,
            'price_type_in' => $this->resource->price_type_in
        ];
    }
}
