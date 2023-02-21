<?php

namespace App\Http\Controllers\Auxliliar;

use App\Http\Controllers\Controller;
use App\Models\Auxiliar\TypeReg;
use Illuminate\Http\Request;

class TipoRegistroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    //
    public function index(){
        return view('auxiliar.treg.index');
    }

    public function getTable(){
        $data = array();
        $headers = array();

        $datos = TypeReg::all();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            // $buttons .= "<a class='btn btn-dark m-1' href='javascript:generateNew(" . $value->id . ")'>Generar<a>";
            $buttons .= "<a class='btn btn-warning m-1' href='javascript:edit(" . $value->id . ")'>Editar<a>";

            $buttons .= "<a class='btn btn-danger m-1' href='javascript:eliminar(" . $value->id . ")'>Eliminar<a>";

            $data['data'][$key] = [
                $key + 1,
                $value->abr,
                $value->description,
                "<span class='badge badge-primary' style='background-color:$value->color'>$value->color</span>",
                ($value->aditionable)?"<span class='badge badge-success'>Si</span>":"<span class='badge badge-danger'>No</span>",
                ($value->is_paid)?"<span class='badge badge-success'>Pagado</span>":"<span class='badge badge-danger'>No Pagado</span>",
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
            try {
                //code...
                $proceso = new TypeReg();
                $proceso->aditionable = (isset($req->aditionable))?true:false;
                $proceso->is_paid = (isset($req->is_uploaded_file))?true:false;
                $proceso->description = $req->name;
                $proceso->abr = $req->abr;
                $proceso->color = $req->color;
                $proceso->save();

                $data["success"] = true;
                $data["data"] = $proceso;
                $data["message"] = "Registrado Correcto";
                return redirect()->route('treg.index');
            } catch (\Throwable $th) {
                //throw $th;
                $data["success"] = false;
                $data["message"] = $th->getMessage();
            }
            return response()->json($data, 200, $headers);
        }
    }

    public function update(Request $req,$id){
        $data = array();
        $headers = array();

        try {
            //code...
            $datos = TypeReg::where('id', '=', $id)->first();

            if ($req->all()) {
                $datos->aditionable = (isset($req->aditionable))?true:false;
                $datos->is_paid = (isset($req->is_paid))?true:false;
                $datos->description = $req->name;
                $datos->abr = $req->abr;
                $datos->color = $req->color;
                $datos->save();

                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Editado Correcto";
                return redirect()->route('treg.index');
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
        }
        return response()->json($data, 200, $headers);
    }

    public function delete($id){
        $data = array();
        $headers = array();
        try {
            //code...
            if (is_array($id)) {
            } else {
                $datos = TypeReg::where('id', '=', $id)->delete();
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Eliminado correcto";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
        }
        return response()->json($data, 200, $headers);
    }
}
