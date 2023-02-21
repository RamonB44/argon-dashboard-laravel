<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'firstname',
        'lastname',
        'email',
        'password',
        'address',
        'city',
        'country',
        'postal',
        'about'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Always encrypt the password when it is updated.
     *
     * @param $value
    * @return string
    */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function user_group()
    {
        return $this->belongsToMany('App\Models\Group', 'users_config', 'id_user', 'id_group')->withPivot(['show_aux_treg', 'show_areas', 'sedes','show_function']);
    }

    //return a bool value
    public function hasGroupPermission($key)
    {
        $group_permission = [];
        foreach (Auth::user()->user_group as $k => $v) {
            # code...
            $group_permission = unserialize($v->permission);
        }

        if (!$group_permission) {
            $group_permission = ["viewAsistencia"];
        }

        return in_array($key, $group_permission);
    }
    //return array values
    public function getConfig()
    {
        $config = array();
        // return Auth::user()->user_group;
        foreach (Auth::user()->user_group as $k => $v) {
            $config["treg"] = json_decode($v->pivot->show_aux_treg);
            $config["areas"] = json_decode($v->pivot->show_areas);
            $config['funcion'] = json_decode($v->pivot->show_function);
            $config['sedes'] = json_decode($v->pivot->sedes);
            // return $v;
        }
        return $config;
    }
}
