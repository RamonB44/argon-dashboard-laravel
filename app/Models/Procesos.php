<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procesos extends Model
{
    //
    protected $table = 'procesos';
    protected $fillable = ['name'];

    use SoftDeletes;
}
