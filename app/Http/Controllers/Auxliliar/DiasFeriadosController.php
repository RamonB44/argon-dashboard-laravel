<?php

namespace App\Http\Controllers\Auxliliar;

use App\Http\Controllers\Controller;
use App\Models\Auxiliar\Holidays;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DiasFeriadosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    //
    public function index(){
        return view('auxiliar.offdays.index');
    }

    public function getTable(){
        Carbon::setLocale('es_PE');
        $data = array();
        $headers = array();

        $datos = Holidays::all();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            // $buttons .= "<a class='btn btn-dark m-1' href='javascript:generateNew(" . $value->id . ")'>Generar<a>";
            $buttons .= "<a class='btn btn-warning m-1' href='javascript:edit(" . $value->id . ")'>Editar<a>";

            $buttons .= "<a class='btn btn-danger m-1' href='javascript:eliminar(" . $value->id . ")'>Eliminar<a>";

            $data['data'][$key] = [
                $key + 1,
                Carbon::createFromFormat('d',$value->day)->translatedFormat('l d') . " de " . Carbon::createFromFormat('m',$value->month)->translatedFormat('F') . ( ($value->year)?$value->year:" todos los años" ),
                $buttons
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function create(Request $req){
        $data = array();
        $headers = array();
        if ($req->all()) {

            try {
                //code...
                $proceso = new Holidays();
                $proceso->day = $req->day;
                $proceso->month = $req->month;
                $proceso->year = $req->year;
                $proceso->save();

                $data["success"] = true;
                $data["data"] = $proceso;
                $data["message"] = "Registrado Correcto";
                return redirect()->route('offdays.index');
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
            $datos = Holidays::where('id', '=', $id)->first();

            if ($req->all()) {
                $datos->day = $req->day;
                $datos->month = $req->month;
                $datos->year = $req->year;
                $datos->save();

                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Editado Correcto";
                return redirect()->route('offdays.index');
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
                $datos = Holidays::where('id', '=', $id)->delete();
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
