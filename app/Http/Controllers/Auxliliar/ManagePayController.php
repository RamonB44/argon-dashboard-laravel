<?php

namespace App\Http\Controllers\Auxliliar;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Asistencia;
use Carbon\Carbon;

class ManagePayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $areas = Area::all();
        // dd($areas);
        return view('managePay.index',compact('areas'));
    }

    public function getTable($start_date){

        $data = [];

        $datos = Asistencia::whereDate('created_at',$start_date)->get();

        foreach($datos as $key => $value){
                        $buttons = "";
                        $fecha = Carbon::parse($value->created_at)->format('Y-m-d H:i');
            // $buttons .= "<a class='btn btn-dark m-1' href='javascript:generateNew(" . $value->id . ")'>Generar<a>";
            $buttons .= "<a class='btn btn-outline-success m-1' href='javascript:edit(" . $value->id . ")'>Recalcular Sueldo<a>";

            // $buttons .= "<a class='btn btn-danger m-1' href='javascript:eliminar(" . $value->id . ")'>Eliminar<a>";

            $data['data'][$key] = [
                $key + 1,
                $value->type,
                $value->employes->code,
                $value->employes->doc_num,
                $value->employes->fullname,
                "<input class='form-control' id='pay_$value->id' value='$value->paga' >",
                $fecha,
                $buttons
            ];
        }
        return response()->json($data,200,[]);
    }

    public function recalculate($start_date,$end_date,$sede){
        $fecha1 = Carbon::parse($start_date);
        $fecha2 = Carbon::parse($end_date);

        $days = $fecha1->diffInDays($fecha2) + 1;

        for ($i=0; $i < $days; $i++) {
            # code...
            $fecha = new Carbon($start_date);
            $fecha->addDays($i);
            $asistencia = Asistencia::where('id_sede',$sede)->where('id_aux_treg',1)->whereDate('created_at',$fecha->format('Y-m-d'))->get();
            // dd($asistencia);
            $record_modificaded = [];
            foreach ($asistencia as $key => $value) {
                # code...
                if($value->created_at){
                    $remu_base = !empty($value->employes->remuneracion) ? $value->employes->remuneracion : 0;
                    $inicio = new Carbon($value->created_at);
                    $remu = ($remu_base  +  ($remu_base/6) + (21.7/6)) * 1.1434;
                    if($value->deleted_at){
                        $fin = new Carbon($value->deleted_at);
                        $horas = $inicio->diffInHours($fin);
                        $horas_extras = $horas > 8 ? $horas - 8 : 0;
                        $horas_25 = $horas_extras > 2 ? 2 : $horas_extras;
                        $horas_35 = $horas_extras >= 3 ? 1 : 0;
                        $remu = round(($remu_base + ($remu_base/8 * 1.25 * $horas_25) + ($remu_base/8 * 1.35 * $horas_35) +  ($remu/6) + (21.7/6)) * 1.1434,2);
                    }

                    if($inicio->dayOfWeek == Carbon::SUNDAY){
                        $value->paga = round($remu *2,2);
                    }else{
                        $value->paga = round($remu,2);

                    }
                }
                $value->save();
                $record_modificaded[$key]["DIA ".$fecha->format('Y-m-d')] = $value->id;
            }
        }

        return response()->json($record_modificaded,200,[]);
    }
}
