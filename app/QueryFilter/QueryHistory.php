<?php

namespace App\QueryFilter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QueryHistory extends Model
{
    //
    use SoftDeletes;

    protected $table = "transact_sycn";

    protected $fillable = ['table_name','query','type','from_ip','from_mac','id_user','synchronized',"from_pcname"];
}
