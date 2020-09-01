<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    //
    protected $table = "customer_address";
    protected $fillable = ["customer_id","city_id","district_id","postal_code","lattitude","longitude"];
}
