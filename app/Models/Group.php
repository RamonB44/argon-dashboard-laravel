<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    //
    protected $table = "groups";

    protected $fillable = ['id_user','id_group','group_name','permission'];

    use SoftDeletes;
}
