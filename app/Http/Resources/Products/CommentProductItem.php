<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;
use Carbon\Carbon;

class CommentProductItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::where('id',$this->resource->creator_id)->get();
        return  [
            'id'      => $this->resource->id,
            'comment'     => $this->resource->comment,
            'user_id'      => $this->resource->creator_id,
            'user_name' => $user->first()->name,
            'type'      => $this->resource->type,
            'time' =>  $this->resource->created_at->diffForHumans()
        ];
    }
}
