<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Asistencia;
use App\Models\Gerencia;
use App\Models\Horario;
use App\Models\Procesos;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        if(!Auth::user()->hasGroupPermission("viewArea")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        $gerencia = Gerencia::all();
        return view('area.index',compact('gerencia'));
    }

    public function getTable($id_sede = 1,$id_proceso = 1){
        $data = array();
        $headers = array();

        $datos = Area::join('areas_sedes', function (JoinClause $join) use ($id_sede,$id_proceso){
            $join->on('areas_sedes.id_area','=','areas.id')
            ->where('areas_sedes.id_sede',$id_sede)
            ->where('areas_sedes.id_proceso',$id_proceso);
        })->join('procesos','areas_sedes.id_proceso','procesos.id')
        ->select('areas.*','procesos.name as proceso',DB::raw('areas_sedes.c_costo as ccosto, areas_sedes.id as id_areas_sedes,areas_sedes.id_proceso as id_proceso'))
        ->get();

        foreach ($datos as $key => $value) {
            # code...
            // $buttons .= "<a class='btn btn-dark m-1' href='javascript:generateNew(" . $value->id . ")'>Generar<a>";
            $buttons = "";
            $buttons .= "<a class='btn btn-warning m-1' href='javascript:edit(" . $value->id . ")'>Editar<a>";
            $buttons .= "<a class='btn btn-danger m-1' href='javascript:eliminar(" . $value->id_areas_sedes . ")'>Eliminar<a>";
            $ccosto = "";
            $costos = json_decode($value->ccosto);

            foreach($costos as $k => $v){
                $ccosto.="<span class='badge badge-primary' >$v</span>";
            }

            $data['data'][$key] = [
                $key + 1,
                $value->gerencia->description,
                $value->area,
                "<span style='background-color: $value->color' class='badge badge-secondary' >$value->color</span>",
                $value->first_out_group == 1 ? "DIRECTO": "INDIRECTO",
                $ccosto,
                $value->proceso,
                $buttons
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function create(Request $req){
        $data = array();
        $headers = array();
        if ($req->all()) {
            // return $req->all();
            if(!Auth::user()->hasGroupPermission('createArea')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            try {
                //code...
                $area = Area::where('area',$req->name)->first();
                if($area){
                    $costos = empty($req->c_costo_c) ? [] : $req->c_costo_c;
                    $areas_sedes = DB::table('areas_sedes')->where('id_area',$area->id)->where('id_sede',$req->id_sede)->where('id_proceso',$req->proceso)->get();
                    // $area->areas_sedes()->attach(array($req->id_sede => array('c_costo' => json_encode($costos) , 'id_proceso' => $req->proceso)));
                    if(count($areas_sedes) == 0){
                        $proceso = DB::table('areas_sedes')->updateOrInsert(['id_area'=>$area->id,'id_sede'=> $req->id_sede , 'id_proceso'=>$req->proceso],[
                            'c_costo' => json_encode($costos)
                        ]);
                        $data["success"] = true;
                        $data["data"] = $proceso;
                        $data["message"] = "Registrado Correcto";
                        $data["icon"] = "success";
                        $data["title"] = "Correcto";
                    }else{
                        $data["success"] = true;
                        $data["message"] = "Duplicado";
                        $data["icon"] = "warning";
                        $data["title"] = "Incorrecto";
                    }
                }

            } catch (\Throwable $th) {
                //throw $th;
                $data["success"] = false;
                $data["message"] = $th->getMessage();
                $data["title"] = "Error";
                $data["icon"] = "error";
            }
            return response()->json($data, 200, $headers);
        }
    }

    public function update(Request $req,$id,$id_sede = 1,$id_proceso = 1){
        $data = array();
        $headers = array();
        try {
            //code...
            $datos = Area::join('areas_sedes', function (JoinClause $join) use ($id_sede,$id_proceso){
                $join->on('areas_sedes.id_area','=','areas.id')
                ->where('areas_sedes.id_sede',$id_sede)
                ->where('areas_sedes.id_proceso',$id_proceso);
            })
            ->where('areas_sedes.id_area',$id)
            ->select('areas.*',DB::raw('areas_sedes.c_costo as ccosto, areas_sedes.id as id_areas_sedes, areas_sedes.id_proceso as id_proceso'))
            ->first();
            // $datos = DB::table('areas_sedes')->where('id',$id)->first();
            // dd($datos);
            // $datos->areas_sedes;
            // dd($datos->areas_sedes->first()->pivot->ccosto);
            if ($req->all()) {
                if(!Auth::user()->hasGroupPermission('updateArea')){
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                $datos->area = $req->name;
                $datos->id_gerencia = $req->gerencia;
                $datos->hora_ingreso = $req->hora_ingreso;
                $datos->hora_salida = $req->hora_salida;
                // $datos->id_area = 1;
                $datos->save();
                $costos = empty($req->c_costo_c) ? [] : $req->c_costo_c;
                // $datos->areas_sedes()->sync(array($id_sede => array('c_costo' => json_encode($costos),'id_proceso' => $req->id_proceso )) );
                DB::table('areas_sedes')->updateOrInsert(['id_area'=> $datos->id,'id_sede'=> $id_sede , 'id_proceso'=> $id_proceso],[
                    'c_costo' => json_encode($costos)
                ]);
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Editado Correcto";
                $data["icon"] = "success";
                $data["title"] = "Correcto";
                // return $data;
            } else {
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Consultado Correcto";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
            $data["title"] = "Error";
            $data["icon"] = "error";
        }
        return response()->json($data, 200, $headers);
    }

    public function delete($id,$id_sede = 1){
        $data = array();
        $headers = array();
        try {
            if(!Auth::user()->hasGroupPermission('deleteArea')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            //code...
            if (is_array($id)) {
            } else {
                // $datos = Area::join('areas_sedes', function (JoinClause $join) use ($id_sede){
                //     $join->on('areas_sedes.id_area','=','areas.id')
                //         ->where('areas_sedes.id_sede',$id_sede);
                // })
                // ->where('areas.id',$id)
                // ->select('areas.*',DB::raw('areas_sedes.c_costo as ccosto'))
                // ->first();

                // $datos->areas_sedes()->detach($id_sede);
                $datos = DB::table('areas_sedes')->where('id',$id)->delete();

                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Eliminado correcto";
                $data["icon"] = "success";
                $data["title"] = "Correcto";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
            $data["title"] = "Error";
            $data["icon"] = "error";
        }
        return response()->json($data, 200, $headers);
    }

    public function group(Request $req){
        $data = array();

        $headers = array();
        // dd($req->ids);
        $datos = Area::whereIn('area',collect($req->ids))->get();

        foreach ($datos as $key => $value) {
            # code...
            $programaciones = Horario::where('id_area','=',$value->id)->count();

            $data['data'][$key] = [
                $value->id,
                $value->area,
                $programaciones
            ];
        }

        return response()->json($data, 200, $headers);
    }

    public function getData($id_gerencia){
        $areas = Area::select('area as description','id')->get();
        if($id_gerencia!=0){
            $areas = Area::select('area as description','id')->where('id_gerencia','=',$id_gerencia)->get();
        }

        return response()->json($areas, 200, []);
    }

    public function loadCCosto($id_sede = 1,$id_proceso = 1,$id_area = 1){
        $areas = Area::join('areas_sedes', function (JoinClause $join) use ($id_sede,$id_proceso){
            $join->on('areas_sedes.id_area','=','areas.id')
            ->where('areas_sedes.id_sede',$id_sede)
            ->where('areas_sedes.id_proceso',$id_proceso);
        })
        ->select(DB::raw('areas_sedes.c_costo as ccosto, areas_sedes.id_proceso as id_proceso'))
        ->get();

        return response()->json($areas,200,[]);
    }

    public function loadAreas($id_sede = 1,$id_proceso = 1){
        $areas = Area::join('areas_sedes', function (JoinClause $join) use ($id_sede,$id_proceso){
            $join->on('areas_sedes.id_area','=','areas.id')
            ->where('areas_sedes.id_sede',$id_sede)
            ->where('areas_sedes.id_proceso',$id_proceso);
        })
        ->groupBy('id_area')
        ->select(DB::raw('areas_sedes.id_area as id, areas.area as area'))
        ->get();

        return response()->json($areas,200,[]);
    }

    public function gestion(){
        if(!Auth::user()->hasGroupPermission("viewArea")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        $gerencia = Gerencia::all();
        return view('area.gestion',compact('gerencia'));
    }

    public function gestiongetTable(){
        $data = array();
        $headers = array();

        $datos = Area::all();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            // $buttons .= "<a class='btn btn-dark m-1' href='javascript:generateNew(" . $value->id . ")'>Generar<a>";
            $buttons .= "<a class='btn btn-warning m-1' href='javascript:edit(" . $value->id . ")'>Editar<a>";

            $buttons .= "<a class='btn btn-danger m-1' href='javascript:eliminar(" . $value->id . ")'>Eliminar<a>";

            $data['data'][$key] = [
                $key + 1,
                $value->gerencia->description,
                $value->area,
                "<span style='background-color:$value->color' class='badge badge-primary'>$value->color</span>",
                $value->first_out_group == 1 ? "DIRECTO":"INDIRECTO",
                ($value->hora_ingreso)?$value->hora_ingreso:"NO DEFINIDA",
                ($value->hora_salida)?$value->hora_salida:"NO DEFINIDA",
                $buttons
            ];
        }
        // Carbon::now()->
        return response()->json($data, 200, $headers);
    }

    public function gestioncreate(Request $req){
        $data = array();
        $headers = array();
        if ($req->all()) {
            if(!Auth::user()->hasGroupPermission('createArea')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            try {
                //code...
                $proceso = new Area();
                $proceso->id_gerencia = $req->gerencia;
                $proceso->area = $req->name;
                $proceso->first_out_group = $req->tcosto;
                $proceso->color = $req->colorpicker;
                $proceso->hora_ingreso = $req->hora_ingreso;
                $proceso->hora_salida = $req->hora_salida;
                $proceso->save();

                $data["success"] = true;
                $data["data"] = $proceso;
                $data["message"] = "Registrado Correcto";
                $data["icon"] = "success";
                $data["title"] = "Correcto";
            } catch (\Throwable $th) {
                //throw $th;
                $data["success"] = false;
                $data["message"] = $th->getMessage();
                $data["title"] = "Error";
                $data["icon"] = "error";
            }
            return response()->json($data, 200, $headers);
        }
    }

    public function gestionupdate(Request $req,$id){
        $data = array();
        $headers = array();
        try {
            //code...
            $datos = Area::where('id', '=', $id)->first();

            if ($req->all()) {
                if(!Auth::user()->hasGroupPermission('updateArea')){
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                $datos->area = $req->name;
                $datos->id_gerencia = $req->gerencia;
                $datos->first_out_group = $req->tcosto;
                $datos->color = $req->colorpicker;
                $datos->hora_ingreso = $req->hora_ingreso;
                $datos->hora_salida = $req->hora_salida;
                // $datos->id_area = 1;
                $datos->save();

                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Editado Correcto";
                $data["icon"] = "success";
                $data["title"] = "Correcto";
                // return $data;
            } else {
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Consultado Correcto";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
            $data["title"] = "Error";
            $data["icon"] = "error";
        }
        return response()->json($data, 200, $headers);
    }

    public function gestiondelete($id){
        $data = array();
        $headers = array();
        try {
            if(!Auth::user()->hasGroupPermission('deleteArea')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            //code...
            if (is_array($id)) {
            } else {
                $datos = Area::where('id', '=', $id)->delete();
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Eliminado correcto";
                $data["icon"] = "success";
                $data["title"] = "Correcto";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
            $data["title"] = "Error";
            $data["icon"] = "error";
        }
        return response()->json($data, 200, $headers);
    }

    public function getFunctions(Request $req){
        return response()->json($req->all(),200,[]);
    }
}
