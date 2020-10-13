<?php

namespace App\Models\Sistem;

use Illuminate\Database\Eloquent\Model;

class DistanceRates extends Model
{
    protected $table = "distance_rates";
    protected $filable = ["km","price","min_km","max_kmd"];
}
