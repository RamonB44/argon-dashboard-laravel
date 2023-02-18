<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    //
    protected $table = "areas";
    protected $fillable = ['area','id_gerencia','since_at','until_at'];

    public function gerencia()
    {
        return $this->belongsTo('App\Gerencia', 'id_gerencia');
    }

    public function areas_sedes()
    {
        return $this->belongsToMany('App\Sedes', 'areas_sedes', 'id_area', 'id_sede')->withPivot(['id','c_costo','id_proceso']);
    }
}
