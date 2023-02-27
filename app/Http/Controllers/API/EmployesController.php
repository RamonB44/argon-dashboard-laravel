<?php

namespace App\Http\Controllers\API;

use App\Area;
use App\Funcion;
use App\Sede;
use App\Procesos;
use App\Asistencia;
use App\Employes;
use App\Horario;
use App\Http\Controllers\Controller;
use App\Sedes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Exception;

class EmployesController extends Controller
{
    private static $zToken =  "G5NsOhtoAv3zauWwjJUJqOFAfoXw4ADnn5yfybad2I7DqtPx16ea7tgFdI22AWiO6vFYQhNxTpp3veSAcuMCJ0s6GZ3AlWWTZRav";
    public function filterEmploye(Request $req,$token){
        
        try{
            $codigos = json_decode($req->codigos);// get array from json
            
            $funcion_area = Funcion::join('area','funct_area.id_area','area.id')->join('function','funct_area.id_function','function.id')->where('function.id',$req->funcion_code)->where('area.id',$req->area_code)->firstOrFail();
            
            $area = Area::where('id',$req->area_code)->firstOrFail();
            $sede = Sedes::where('id',$req->sede_code)->firstOrFail();
            $proceso = Procesos::where('id',$req->proceso_code)->firstOrFail();
            
            $c_costos = json_decode(DB::table('areas_sedes')->where('area_id',$area->id)->where('proceso_id',$proceso->id)->where('sede_id',$sede->id)->first()->c_costo);
            
            $c_costo = in_array($req->c_costo,$c_costos);
            
            $empl = Employes::whereIn('code',$codigos)->orWhereIn('doc_num',$codigos)->get()->toArray();
            if(empty($empl)){
                $response["success"] = false;
                $response["message"] = "El codigo de empleado ".$codigo." no existe, comunicar inmediatamente a recursos humanos.";
                // $response["data"] = 
            }else{
                $response["success"] = true;
                $response["message"] = "Empleado encontrado.";
                $response["updateSuccess"] = true;
                if($req->all() && $update){
                    //update employe data
                    Employes::whereIn('code',$codigos)->orWhereIn('doc_num',$codigos)->update([
                        'function_id' => $funcion_area->id,
                        // 'area_id' => $area->id,
                        'sede' => $sede->id,
                        'proceso' => $proceso->id,
                        'c_costo' => ($c_costo ? $req->c_costo : null),
                        ]);
                }
                $response["data"] = $empl;
            }
        }
        catch(Exception $ex){
            
        }
    }
}

