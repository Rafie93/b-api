<?php

namespace App\Http\Resources\Sistem;

use Illuminate\Http\Resources\Json\JsonResource;

class BankItem extends JsonResource
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
            'id'      => $this->resource->id,
            'bank_name'     => $this->resource->bank_name,
            'bank_account_name'     => $this->resource->bank_account_name,
            'bank_account_no'     => $this->resource->bank_account_no,
            'description'     => $this->resource->description,
            'logo'  => $this->resource->logo(),
        ];
    }
}
