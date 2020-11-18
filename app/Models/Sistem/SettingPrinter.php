<?php

namespace App\Models\Sistem;

use Illuminate\Database\Eloquent\Model;

class SettingPrinter extends Model
{
    protected $table = "setting_printer";
    protected $fillable = ["user_id","app_key","app_port","printer_name","ip_address","port","type","connector"];
}
