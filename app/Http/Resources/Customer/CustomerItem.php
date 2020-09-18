<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerItem extends JsonResource
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
            'value'    => $this->resource->id,
            'code'        => $this->resource->code,
            'name'        => $this->resource->name,
            'label'      => $this->resource->name,
            'point'      => $this->resource->point,
            'type'      => $this->resource->type,
            'phone' => $this->resource->user->phone,
            'email' => $this->resource->user->email,
            'birthday' => $this->resource->user->birthday,
            'username'  => $this->resource->user->username,
            'gender'  => $this->resource->user->gender,

        ];
    }
}
