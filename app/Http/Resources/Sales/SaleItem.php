<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Sales\SaleDetail;

class SaleItem extends JsonResource
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
            'code'        => $this->resource->code,
            'coupon'        => $this->resource->coupon,
            'customer_id'      => $this->resource->customer_id,
            'customer_name'      => $this->resource->customerName(),
            'date'      => $this->resource->date,
            'time'      => $this->resource->time,
            'payment_methode' => $this->resource->payment_methode,
            'status' => $this->resource->status,
            'total_before_tax' => $this->resource->total_before_tax,
            'total_price' => $this->resource->total_price,
            'total_price_product' => $this->resource->total_price_product,
            'total_service' => $this->resource->total_service,
            'total_shipping' => $this->resource->total_shipping,
            'total_tax' => $this->resource->total_tax,
            'total_item'=> SaleDetail::where('sale_id',$this->resource->id)->get()->count()
        ];
    }
}
