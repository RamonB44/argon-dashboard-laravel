<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    //
    protected $table = "reg_horarios";

    use SoftDeletes;

    public function areas()
    {
        return $this->belongsTo('App\Area', 'id_area');
    }
}
