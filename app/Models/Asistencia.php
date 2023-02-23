<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asistencia extends Model
{
    //
    public $timestamps = false;

    use SoftDeletes;

    protected $dates = ["created_at", "updated_at", 'deletedAt'];

    const DELETED_AT = 'deletedAt';

    protected $table = "reg_assistance";

    protected $fillable = ["uniqueReg","checked","checked_at","id_user_checked","synchronized_users","basico", "code", "id_employe", "id_function","dir_ind","type","id_employe_type" ,"id_proceso", "c_costo","id_sede", "id_aux_treg", "temperature","synchronized_users" , "checked" , "created_at", "updated_at", "deleted_at","deletedAt","created_at_search"];

    protected $sync = true;

    public function employes()
    {
        return $this->belongsTo('App\Models\Employes', 'id_employe')->orWhere('code',$this->code)->withTrashed();
    }

    public function aux_type()
    {
        return $this->belongsTo('App\Models\Auxiliar\TypeReg', 'id_aux_treg');
    }

    public function sede()
    {
        return $this->belongsTo('App\Models\Sedes', 'id_sede');
    }

    public function funcion(){
        return $this->belongsTo('App\Models\Funcion','id_function');
    }

    public function user(){
        return $this->belongsTo('App\Models\User','id_user_checked');
    }
}
