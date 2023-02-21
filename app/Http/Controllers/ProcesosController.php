<?php

namespace App\Http\Controllers;

use App\Models\Procesos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcesosController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        if(!Auth::user()->hasGroupPermission("viewProcesos")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        return view('procedures.index');
    }

    public function getTable(){
        $data = array();
        $headers = array();

        $datos = Procesos::all();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            // $buttons .= "<a class='btn btn-dark m-1' href='javascript:generateNew(" . $value->id . ")'>Generar<a>";
            $buttons .= "<a class='btn btn-warning m-1' href='javascript:edit(" . $value->id . ")'>Editar<a>";

            $buttons .= "<a class='btn btn-danger m-1' href='javascript:eliminar(" . $value->id . ")'>Eliminar<a>";

            $data['data'][$key] = [
                $key + 1,
                $value->name,
                $buttons
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function create(Request $req){
        $data = array();
        $headers = array();
        if ($req->all()) {
            if(!Auth::user()->hasGroupPermission('createProcesos')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            try {
                //code...
                $proceso = new Procesos();
                $proceso->name = $req->name;
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
            $datos = Procesos::where('id', '=', $id)->first();

            if ($req->all()) {
                if(!Auth::user()->hasGroupPermission('updateProcesos')){
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                $datos->name = $req->name;
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
            if(!Auth::user()->hasGroupPermission('deleteProcesos')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            if (is_array($id)) {
            } else {
                $datos = Procesos::where('id', '=', $id)->delete();
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

    public function getData(){
        $option = [];
        $data = Procesos::select(DB::raw('id,name'))->get();
        foreach ($data as $key => $value) {
            # code...
            $option[$value->id] = $value->name;
        }
        return response()->json($option, 200, []);
    }
}
