<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\User;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','username','phone','is_active','fcm_token','role_id','birthday','gender','image','penanggung_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function role()
    {
        $role_display = "";
        switch ($this->role_id) {
            case 1:
              $role_display="Admin";
              break;
            case 2:
                $role_display="Admin";
              break;
            case 3:
                $role_display="Kasir";
              break;
            case 4:
                $role_display="Store";
              break;
            case 5:
                $role_display="Keuangan";
              break;
            case 6:
                $role_display="Logistik";
              break;
            case 7:
                $role_display="Manajer Bisnis";
              break;
            case 7:
                $role_display="Manajer Operasional";
              break;
            case 10:
                $role_display="Customer";
              break;
            default:
              $role_display="Customer";
          }
        return $role_display;
    }
    public function penanggung()
    {
        $penanggung_id = $this->penanggung_id;
        if($penanggung_id==null){
            return "-";
        }else{
            return User::find($penanggung_id)->first()->name;
        }
    }
    public function status()
    {
        return $this->is_active==1 ? "Aktif" : 'Tidak Aktif';
    }
}
