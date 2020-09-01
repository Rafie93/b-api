<?php

namespace App\Models\Sistem;

use Illuminate\Database\Eloquent\Model;

class NumberSequence extends Model
{
    protected $table = "number_sequence";
    protected $fillable = ["seq_name","seq_value","seq_year"];
}
