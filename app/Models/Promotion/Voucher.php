<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = "voucher";
    protected $fillable = ["code_voucher","description","jenis_voucher","is_active","nilai","maksimal","maksimal","jenis_nilai","berlaku_start","berlaku_end"];
}
