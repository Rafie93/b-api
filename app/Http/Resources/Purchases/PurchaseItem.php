<?php

namespace App\Http\Resources\Purchases;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Purchases\PurchaseDetail;

class PurchaseItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $od = PurchaseDetail::where('purchase_id',$this->resource->id)->get();
        $det=[];
        foreach($od as $d){
            $det[] = array(
                'product_id' => $d->product_id,
                'sku' => $d->product->sku,
                'barcode'=> $d->product->barcode,
                'name' => $d->product->name,
                'price' => $d->price,
                'quantity' => $d->quantity,
                'quantity_received' => $d->quantity_received,
                'expired_date' => $d->exp_date,
                'unit'  => $d->unit,
                'status_barang'  => $d->status,
                'notes' => $d->notes
            );
        }

        return  [
            'id'        => $this->resource->id,
            'code' => $this->resource->code,
            'notes'      => $this->resource->notes,
            'date' => $this->resource->date,
            'status'   => $this->resource->status,
            'status_display'      => $this->resource->status(),
            'creator_id' => $this->resource->creator_id,
            'creator_display' => $this->resource->creator(),
            'receive_id' => $this->resource->receive_id,
            'receive_name' => $this->resource->receive(),
            'receive_date' => $this->resource->receive_date,
            'supplier_id' => $this->resource->supplier_id,
            'supplier_name' => $this->resource->supplier(),
            'approved_date'=> $this->resource->approved_date,
            'grand_total' => $this->resource->grand_total,
            'detail' => $det
        ];
    }
}
