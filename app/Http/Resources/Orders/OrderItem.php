<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Orders\OrderDetail;
class OrderItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $od = OrderDetail::where('order_product_id',$this->resource->id)->get();
        $det=[];
        foreach($od as $d){
            $det[] = array(
                'product_id' => $d->id,
                'sku' => $d->product->sku,
                'barcode'=> $d->product->barcode,
                'name' => $d->product->name,
                'quantity' => $d->quantity_order,
                'unit'  => $d->unit,
                'notes' => $d->notes
            );
        }

        return  [
            'id'        => $this->resource->id,
            'code' => $this->resource->code,
            'code_gudang' => $this->resource->code_gudang,
            'notes'      => $this->resource->notes,
            'date' => $this->resource->date,
            'status'   => intval($this->resource->status),
            'status_display'      => $this->resource->status(),
            'approved_date' => $this->resource->approved_date,
            'approved_order_date' => $this->resource->approved_order_date,
            'creator_id' => $this->resource->creator_id,
            'creator_display' => $this->resource->creator(),
            'penanggung_jawab' => $this->resource->penanggung_jawab(),
            'approved_id' => $this->resource->approved_id,
            'approved_display' => $this->resource->approved(),
            'send_id' => $this->resource->send_id,
            'send_date' => $this->resource->send_date,
            'proses_date' => $this->resource->proses_date,
            'arrival_date' => $this->resource->arrival_date,
            'detail' => $det

        ];
    }
}
