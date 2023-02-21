<?php

namespace App\Http\Controllers;

use App\Models\Sedes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SedesController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        if(!Auth::user()->hasGroupPermission("viewSedes")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        return view('sedes.index');
    }

    public function getTable(){
        $data = array();
        $headers = array();

        $datos = Sedes::all();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            // $buttons .= "<a class='btn btn-dark m-1' href='javascript:generateNew(" . $value->id . ")'>Generar<a>";
            $buttons .= "<a class='btn btn-warning m-1' href='javascript:edit(" . $value->id . ")'>Editar<a>";

            $buttons .= "<a class='btn btn-danger m-1' href='javascript:eliminar(" . $value->id . ")'>Eliminar<a>";

            $data['data'][$key] = [
                $key + 1,
                $value->name,
                $value->zona,
                $value->t_sede,
                $buttons
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function create(Request $req){
        $data = array();
        $headers = array();
        if ($req->all()) {

            if(!Auth::user()->hasGroupPermission('createSedes')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            try {
                //code...
                $proceso = new Sedes();
                $proceso->name = $req->name;
                $proceso->zona = $req->zona;
                $proceso->t_sede = $req->t_sede;
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
            $datos = Sedes::where('id', '=', $id)->first();

            if ($req->all()) {
                if(!Auth::user()->hasGroupPermission('updateSedes')){
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                $datos->name = $req->name;
                $datos->zona = $req->zona;
                $datos->t_sede = $req->t_sede;
                // $datos->id_area = 1;
                $datos->save();

                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Editado Correcto";
                $data["icon"] = "success";
                $data["title"] = "Correcto";
                // return redirect()->route('sedes.index');
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
            $data["icon"] = "error";
            $data["title"] = "Error";
        }
        return response()->json($data, 200, $headers);
    }

    public function delete($id){
        $data = array();
        $headers = array();
        if(!Auth::user()->hasGroupPermission('updateSedes')){
            $data["success"] = false;
            $data["message"] = "No tienes los permisos sufienctes para esta accion";
            $data["icon"] = "warning";
            $data["title"] = "Authorization Denied";
            return response()->json($data, 200, []);
        }
        try {
            //code...
            if (is_array($id)) {
            } else {
                $datos = Sedes::where('id', '=', $id)->delete();
                $data["success"] = true;
                $data["data"] = $datos;
                $data["title"] = "Correcto";
                $data["icon"] = "success";
                $data["message"] = "Eliminado correcto";
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
