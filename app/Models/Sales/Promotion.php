<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table ='promotion';
    protected $fillable = ['date_start','date_end','description','min_shopping','type_promo','option_promo','total','is_active'];
}
