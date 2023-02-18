<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employes extends Model
{
    use SoftDeletes;
    //
    protected $table = "employes";
    protected $fillable = ['id','synchronized_users','code','valid','id_proceso','id_function','type','doc_num','fullname','c_costo','id_sede','dir_ind','id_employe_type','turno','hasChildren','telephone_num','remuneracion','created_at','deleted_at'];

    public function funcion()
    {
        return $this->belongsTo('App\Funcion', 'id_function');
    }

    public function procesos()
    {
        return $this->belongsTo('App\Procesos', 'id_proceso');
    }

    public function sedes()
    {
        return $this->belongsTo('App\Sedes', 'id_sede');
    }

    public function employes_type(){
        return $this->belongsTo('App\EmployesType','id_employe_type');
    }

    public function employes_process(){
        return $this->belongsToMany('App\Procesos','procesos_employe','id_employe','id_proceso')->withPivot(['id','until_at','deleted_at'])->withTimestamps();
    }

}
