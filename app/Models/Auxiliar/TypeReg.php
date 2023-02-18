<?php

namespace App\Models\Auxiliar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeReg extends Model
{
    //
    protected $table = "aux_type_reg";
    use SoftDeletes;
}
