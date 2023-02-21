<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Funcion;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FuncionController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        if(!Auth::user()->hasGroupPermission("viewFuncion")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        $area = Area::all();
        return view('funcion.index',compact('area'));
    }

    public function getTable(){
        $data = array();
        $headers = array();

        $datos = Funcion::all();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            // $buttons .= "<a class='btn btn-dark m-1' href='javascript:generateNew(" . $value->id . ")'>Generar<a>";
            $buttons .= "<a class='btn btn-warning m-1' href='javascript:edit(" . $value->id . ")'>Editar<a>";

            $buttons .= "<a class='btn btn-danger m-1' href='javascript:eliminar(" . $value->id . ")'>Eliminar<a>";

            $data['data'][$key] = [
                $key + 1,
                (empty($value->areas))?"REQUIERE ASIGNAR AREA":$value->areas->area,
                $value->description,
                $buttons
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function create(Request $req){
        $data = array();
        $headers = array();
        if ($req->all()) {
            if(!Auth::user()->hasGroupPermission('createFuncion')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            try {
                //code...
                $proceso = new Funcion();
                $proceso->id_area = $req->area;
                $proceso->id_function = $req->funcion;
                $proceso->description = DB::table('function')->find($req->funcion)->description;
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

    public function update(Request $req,$id){
        $data = array();
        $headers = array();

        try {
            //code...
            $datos = Funcion::where('id', '=', $id)->first();

            if ($req->all()) {
                if(!Auth::user()->hasGroupPermission('updateFuncion')){
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                $datos->id_area = $req->area;
                $datos->id_function = $req->funcion;
                $datos->description = DB::table('function')->find($req->funcion)->description;
                $datos->save();
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

    public function delete($id){
        $data = array();
        $headers = array();
        try {
            //code...
            if(!Auth::user()->hasGroupPermission('deleteFuncion')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            if (is_array($id)) {
            } else {
                $datos = Funcion::where('id', '=', $id)->delete();
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

    public function loadByArea($id_area,$id_sede = 1,$id_proceso = 1){
        $data = array();
        $data["funcion"] = array();
        $data["ccosto"] = array();
        if($id_area!="null"){
            $data["funcion"] = Funcion::where('id_area','=',$id_area)->get();
            $data["ccosto"] = Area::join('areas_sedes', function (JoinClause $join) use ($id_sede,$id_proceso){
                $join->on('areas_sedes.id_area','=','areas.id')
                ->where('areas_sedes.id_sede',$id_sede)
                ->where('areas_sedes.id_proceso',$id_proceso);
            })
            ->where('areas_sedes.id_area',$id_area)
            ->select(DB::raw('areas_sedes.c_costo as ccosto'))
            ->first();
        }

        return response()->json($data, 200, []);
    }

    public function ggetTable(){
        $data = array();
        $headers = array();

        $datos = DB::table('function')->get();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            // $buttons .= "<a class='btn btn-dark m-1' href='javascript:generateNew(" . $value->id . ")'>Generar<a>";
            $buttons .= "<a class='btn btn-warning m-1' href='javascript:edit(" . $value->id . ")'>Editar<a>";

            $buttons .= "<a class='btn btn-danger m-1' href='javascript:eliminar(" . $value->id . ")'>Eliminar<a>";

            $data['data'][$key] = [
                $key + 1,
                $value->description,
                $buttons
            ];
        }

        return response()->json($data, 200, $headers);
    }

    public function gcreate(Request $req){
        $data = array();
        $headers = array();
        if ($req->all()) {
            if(!Auth::user()->hasGroupPermission('createFuncion')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            try {
                //code...
                $proceso = DB::table('function')->insert(["description"=> $req->funcion]);
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

    public function gupdate(Request $req,$id){
        $data = array();
        $headers = array();

        try {
            //code...
            $datos = DB::table('function')->find($id);
            //$datos = Funcion::where('id', '=', $id)->first();

            if ($req->all()) {
                if(!Auth::user()->hasGroupPermission('updateFuncion')){
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                DB::table('function')->where('id',$id)->update(['description' => $req->funcion]);
                //$datos->description = $req->funcion;
                //$datos->save();

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
            //dd($th);
            $data["success"] = false;
            $data["message"] = $th->getMessage();
            $data["title"] = "Error";
            $data["icon"] = "error";
        }
        return response()->json($data, 200, $headers);
    }

    public function gdelete($id){
        $data = array();
        $headers = array();
        try {
            //code...
            if(!Auth::user()->hasGroupPermission('deleteFuncion')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            if (is_array($id)) {
            } else {
                $datos = DB::table('function')->find($id)->delete();;
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
}
