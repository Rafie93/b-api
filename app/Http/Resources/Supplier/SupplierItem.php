<?php

namespace App\Http\Resources\Supplier;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Products\SupplierProduct;
class SupplierItem extends JsonResource
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
            'value'        => $this->resource->id,
            'label'      => $this->resource->name,
            'name'      => $this->resource->name,
            'phone' => $this->resource->phone,
            'email' => $this->resource->email,
            'address' => $this->resource->address,
            'pic' => $this->resource->pic,
            'total_product'=> SupplierProduct::where('supplier_id',$this->resource->id)->get()->count()
        ];
    }
}
