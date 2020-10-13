<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserItem extends JsonResource
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
            'value'        => $this->resource->id,
            'label'      => $this->resource->name,
            'name'      => $this->resource->name,
            'username' => $this->resource->username,
            'phone' => $this->resource->phone,
            'role_id'   => strval($this->resource->role_id),
            'role_display' => $this->resource->role(),
            'penanggung_id' => $this->resource->penanggung_id,
            'penanggung_label'=>$this->resource->penanggung(),
            'gender' => $this->resource->gender,
            'birthday' => $this->resource->birthday,
            'email' => $this->resource->email,
            'address' => $this->resource->address,
            'status_txt' => $this->resource->status(),
            'fcm_token' => $this->resource->fcm_token,
            'image' => $this->resource->image()
        ];
    }
}
