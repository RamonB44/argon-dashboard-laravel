<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Funcion extends Model
{
    //
    protected $table = "funct_area";
    protected $fillable = ['description','id_area','id_function'];

    public function areas()
    {
        return $this->belongsTo('App\Models\Area', 'id_area');
    }
}
