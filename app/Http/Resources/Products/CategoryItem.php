<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $parent_id = $this->resource->parent_id;
        if($parent_id==null){
            return  [
                'value'        => $this->resource->id,
                'label'      => $this->resource->name,
                'image'      => $this->resource->image(),
            ];
        }else{
            return  [
                'value'        => $this->resource->id,
                'id'        => $this->resource->id,
                'id_parent' => $this->resource->parent_id,
                'label'      => $this->resource->name,
                'label_parent'   => $this->resource->parent()->name,
                'image'      => $this->resource->image(),
            ];
        }
    }

}
