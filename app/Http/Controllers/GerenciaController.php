<?php

namespace App\Http\Controllers;

use App\Models\Gerencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GerenciaController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        if(!Auth::user()->hasGroupPermission("viewGerencia")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        return view('gerencia.index');
    }


    public function getTable(){
        $data = array();
        $headers = array();

        $datos = Gerencia::all();

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

    public function create(Request $req){
        $data = array();
        $headers = array();
        if ($req->all()) {
            if(!Auth::user()->hasGroupPermission('createGerencia')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            try {
                //code...
                $proceso = new Gerencia();
                $proceso->description = $req->name;
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
            $datos = Gerencia::where('id', '=', $id)->first();

            if ($req->all()) {
                if(!Auth::user()->hasGroupPermission('updateGerencia')){
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                $datos->description = $req->name;
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
            if(!Auth::user()->hasGroupPermission('deleteGerencia')){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            if (is_array($id)) {
            } else {
                $datos = Gerencia::where('id', '=', $id)->delete();
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
        $gerencia = Gerencia::select('description','id')->get();
        return response()->json($gerencia, 200, []);
    }

    //function for reports gerencia

    public function loadAssistance(Request $req){
        //total de dia y noche para gerencia de planta por separado , total verificados y por verificar y faltan asistencias sin filtro de turno
        //fecha de inicio , fin, sedeloadGraphicsbyType
    }

    public function loadGraphicTypeEmploye(Request $req,$id_user = null){
        //retorna la cantidad de asistencias por tipo de empleado jornal o destajo
        //fecha de inicio, fin , sede, turno por defecto segun la horo
    }

    public function loadGraphicTypeCosto(Request $req,$id_user = null){
        //retorna la cantidad de asistencia por tipo de costo directo o indirecto
        //fecha de inicio, fin , sede, turno por defecto segun la hora
        //si existe el id_user entonces se filtrara con esa variable adicional.
    }

    public function loadListasJD_DI(Request $req,$id_user = null){
        //retorna una lista de trabajadores segun jornal o destajo y directo o indirecto.}
        //fecha de inicio , fin, sede, turno por defecto segun la hora
        //si existe el id_user entonces se filtrara con esa variable
    }

    public function loadListadetrabajadores(Request $req,$id_user = null){
        //retorna una lista de trabajadores
        //fecha de inicio, fin , sede , tipo de lista (V,SV,SA,SS), turno por defecto segun la hora.
        //si existe el id_user entonces se filtrara con esa variable
    }
}
