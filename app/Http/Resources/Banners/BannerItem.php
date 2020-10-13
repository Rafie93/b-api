<?php

namespace App\Http\Resources\Banners;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerItem extends JsonResource
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
            'title'      => $this->resource->title,
            'description'     => $this->resource->description,
            'image'  => $this->resource->image(),
        ];
    }
}
