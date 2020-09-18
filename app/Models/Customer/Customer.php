<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $table = "customer";
    protected $fillable = ["user_id","code","name","point","type"];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
