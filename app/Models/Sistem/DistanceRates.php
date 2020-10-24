<?php

namespace App\Models\Sistem;

use Illuminate\Database\Eloquent\Model;

class DistanceRates extends Model
{
    protected $table = "distance_rates";
    protected $fillable = ["km","prices","min_km","max_km"];
}
