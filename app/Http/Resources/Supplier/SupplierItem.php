<?php

namespace App\Http\Resources\Supplier;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'phone' => $this->resource->phone,
            'email' => $this->resource->email,
            'address' => $this->resource->address
        ];
    }
}
