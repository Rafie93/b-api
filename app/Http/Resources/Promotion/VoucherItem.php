<?php

namespace App\Http\Resources\Promotion;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $now = date('Y-m-d H:i:s');
        $is_expired = false;
        $berlaku_end = $this->resource->berlaku_endtion;
        $berlaku_start = $this->resource->berlaku_start;
        if($berlaku_start != null && $berlaku_end!=null){
            if($now >= $berlaku_start && $now <= $berlaku_end){
                $is_expired = true;
            }else{
                $is_expired = false;
            }
        }

        return  [
            'id'      => $this->resource->id,
            'code_voucher'     => $this->resource->code_voucher,
            'description'     => $this->resource->description,
            'jenis_voucher'     => $this->resource->jenis_voucher,
            'nilai'     => $this->resource->nilai,
            'jenis_nilai'     => $this->resource->jenis_nilai,
            'maksimal'     => $this->resource->maksimal,
            'maksimal_user'     => $this->resource->maksimal_user,
            'berlaku_start'     => $this->resource->berlaku_start,
            'berlaku_end'     => $this->resource->berlaku_end,
            'expired_voucher'  => $is_expired
        ];
    }
}
