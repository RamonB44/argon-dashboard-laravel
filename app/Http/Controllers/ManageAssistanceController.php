<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Asistencia;
// use App\Models\Auxiliar\TypeReg;
use App\Models\Employes;
use App\Models\Auxiliar\TypeReg as AuxiliarTypeReg;
use App\Models\RegEmployes;
use App\Models\RegUnchecked;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManageAssistanceController extends Controller
{
    //public $exception = "";

    public function __construct()
    {
        $this->middleware('auth');

    }
    //
    public function index(){
        if(!Auth::user()->hasGroupPermission("viewHA")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        $areas = Area::all();
        return view('assistance.manage',compact('areas'));
    }

    public function search($code){
        $result = array();
        $employe = Employes::findOrFail($code);
        if($employe){
            //get assistance data from employe
            $datos = Asistencia::where('id_employe','=',$code)
            ->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')
            ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
            DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
            'aux_type_reg.color as borderColor',
            'reg_assistance.created_at as start',
            'reg_assistance.deleted_at as end')->get();
            // dd($datos);
            if($datos){
                //send this in json format
                $result['assistance'] = json_encode($datos);
                $result['employe'] = $employe;
                $result['success'] = true;
                $result['message'] = "Asistencia de empleado encontrada ".$employe->fullname;
            }else{
                $result['employe'] = $employe;
                $result['success'] = false;
                $result['message'] = "No existen registros de asistencia";
            }
        }else{
            $result['success'] = false;
            $result['message'] = "Empleado no esta registrado";
        }

        return response()->json($result, 200, []);
    }

    public function register(Request $req,$id){
        $time = Carbon::now()->timezone('America/Lima');
        $morning = Carbon::create($time->year, $time->month, $time->day, 4, 0, 0); //set time to 04:00 : 4AM
        $evening = Carbon::create($time->year, $time->month, $time->day, 14, 0, 0); //set time to 14:00 : 2PM
        $result = array();
        try {
            if(!Auth::user()->hasGroupPermission("createHA")){
                // return abort('pages.403');
                // return response()->view('pages.403', [], 403);
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                $caledardata = Asistencia::where('id_employe','=',$id)
                ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
                DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
                'aux_type_reg.color as borderColor',
                'reg_assistance.created_at as start',
                'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

                $data['calendardata'] = json_encode($caledardata);
                return response()->json($data, 200, []);
            }
            //code...
            // return $req->all()
            $since_h = Carbon::parse($req->h_since_at);
            $until_h = Carbon::parse($req->h_until_at);

            // $days = $fecha1->diffInDays($fecha2) + 1; // count days bettwen fecha1 and fecha2, adding a day
            //change this with table
            $employe = Employes::where('id','=',$id)->first();
            $aux_treg = AuxiliarTypeReg::where('id','=',$req->description)->first();
            $fecha1 = Carbon::parse($req->d_since_at)->setHours($since_h->format('H'))->setMinutes($since_h->format('i'));
            $fecha2 = Carbon::parse($req->d_until_at)->setHours($until_h->format('H'))->setMinutes($until_h->format('i'));
            $asistencia = Asistencia::where('id_employe', '=', $employe->id)->whereDate('created_at','=',$fecha1->toDateString())->orderBy('id', 'desc')->orderBy('created_at', 'desc')->first();
            // $result['success'] = true;
            $result['title'] = "Correcto";
            $result['icon'] = "success";
            $result['message'] = "Añadido satisfactoriamente";
            if(!$asistencia){
                $newregister = new Asistencia();
                $newregister->id_aux_treg = $req->description;
                $newregister->id_employe = $employe->id;
                $newregister->synchronized_users = "[]";
                $newregister->id_function = $employe->id_function;
                $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                $newregister->id_employe_type = $employe->id_employe_type;
                $newregister->dir_ind = $employe->dir_ind;
                $newregister->type = $employe->type;
                $newregister->id_proceso = $employe->id_proceso;
                $newregister->basico = $employe->remuneracion;
                $newregister->c_costo = $employe->c_costo;
                if($fecha1->between($morning, $evening, true)) {
                    //current time is between morning and evening
                    $newregister->turno = "DIA";
                } else {
                    $newregister->turno = "NOCHE";
                    //current time is earlier than morning or later than evening
                }
                if($req->d_since_at && $req->d_until_at){
                    $mins = $fecha1->diffInMinutes($fecha2, true);
                    // $horas = $ingreso->diffinHours($salida);
                    $horas = $mins/60;
                    // check ingreso is greater than salida
                    if($fecha2->gt($fecha1)){
                        if($horas >= 23){
                            // $this->setSalida($time,$employe);
                            if($aux_treg->id != 1){
                                $newregister->created_at = $fecha1->toDateTimeString();
                                $newregister->deleted_at = $fecha2->toDateTimeString();
                                // $newregister->synchronized_users = "[]";
                                $newregister->save();
                            }else{
                                $result['title'] = "ERROR";
                                $result['icon'] = "error";
                                $result['message'] = "Error la diferencia horaria supera las 23 horas.";
                            }

                        }elseif($horas < 1){
                            //do nothing
                            $result['title'] = "ERROR";
                            $result['icon'] = "error";
                            $result['message'] = "Error la diferencia horaria no supera la hora minima.";
                        }else{
                            $horas = $horas > 8 ? $horas - 1 : $horas;
                            $pago_real = ($employe->remuneracion / 8) * ( $horas >= 8 ? 8 : $horas );
                            // $pago_real = $pago_horas * ;
                            $horas_extras = $horas > 8 ? $horas - 8 : 0;
                            // $horas_25 = $horas_extras > 2 ? 2 : $horas_extras;
                            // $horas_35 = $horas_extras >= 3 ? $horas_extras - 2 : 0;
                            $horas_25 = $h_25 = $horas_extras > 2 ? 2 : $horas_extras;//acumula
                            $horas_35 = $horas_extras > 2 ? $horas_extras - $h_25: 0;
                            $prima = $horas_35 > 1 ?$employe->remuneracion/8 * 1.35 * ($horas_35 - 1):0;
                            $newregister->prima_produccion = $prima;
                            $remu = round(($pago_real + ($employe->remuneracion/8 * 1.25 * $horas_25) + ($employe->remuneracion/8 * 1.35 * $horas_35) +  ($employe->remuneracion/6) + (21.7/6)) * 1.1434,2);
                            if($fecha1->dayOfWeek == Carbon::SUNDAY){
                                $newregister->paga = round($remu *2,2);
                            }else{
                                $newregister->paga = round($remu,2);
                            }
                            // $javas_pas->paga = round(($employe->remuneracion + ($employe->remuneracion/8 * 1.25 * $horas_25) + ($employe->remuneracion/8 * 1.35 * $horas_35) +  ($employe->remuneracion/6) + (21.7/6)) * 1.1434,2);
                            $newregister->created_at = $fecha1->toDateTimeString();
                            $newregister->deleted_at = $fecha2->toDateTimeString();
                            // $newregister->synchronized_users = "[]";
                            $newregister->save();
                        }

                        $reg_un = DB::table('reg_unchecked')->where('reg_code',$employe->code)->whereDate('created_at',$fecha1->toDateString())->first();

                        if($reg_un){
                            $newregister->id_user_checked = $reg_un->id_user;
                            $newregister->checked_at = $reg_un->created_at;
                            $newregister->checked = 1;
                            $newregister->synchronized_users = "[]";
                            $newregister->save();
                        }

                        DB::table('reg_unchecked')->where('reg_code',$employe->code)->whereDate('created_at',$fecha1->toDateString())->delete();
                        DB::table('reg_employes')->where('code',$employe->code)->whereDate('created_at',$fecha1->toDateString())->delete();

                    }else{
                        $result['title'] = "ERROR";
                        $result['icon'] = "error";
                        $result['message'] = "Error fecha inicial mayor a la fecha de cierre.";
                    }
                }
            }else{
                //si es asistencia
                if($asistencia->id_aux_treg == 1 || $asistencia->aux_type->aditionable == 1){
                    //registra adicional
                    //no se puede a���adir dos asistencias seguidas
                    if($aux_treg->id != $asistencia->id_aux_treg){
                        if($aux_treg->aditionable == 0 && $aux_treg->id != 1){
                            Asistencia::where('id_employe', '=', $employe->id)->whereDate('created_at','=',$fecha1->toDateString())->orderBy('id', 'desc')->orderBy('created_at', 'desc')->delete();
                        }
                        $newregister = new Asistencia();
                        $newregister->id_aux_treg = $req->description;
                        $newregister->id_employe = $id;
                        $newregister->synchronized_users = "[]";
                        $newregister->id_function = $employe->id_function;
                        $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                        // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                        $newregister->id_employe_type = $employe->id_employe_type;
                        $newregister->dir_ind = $employe->dir_ind;
                        $newregister->type = $employe->type;
                        $newregister->id_proceso = $employe->id_proceso;
                        $newregister->basico = $employe->remuneracion;
                        $newregister->c_costo = $employe->c_costo;
                        $newregister->created_at = $fecha1->toDateTimeString();
                        $newregister->deleted_at = $fecha2->toDateTimeString();
                        if($fecha1->between($morning, $evening, true)) {
                            //current time is between morning and evening
                            $newregister->turno = "DIA";
                        } else {
                            $newregister->turno = "NOCHE";
                            //current time is earlier than morning or later than evening
                        }
                        if($req->d_since_at && $req->d_until_at){
                            $mins = $fecha1->diffInMinutes($fecha2, true);
                            // $horas = $ingreso->diffinHours($salida);
                            $horas = $mins/60;
                            // check ingreso is greater than salida
                            if($fecha2->gt($fecha1)){

                                $horas = $horas > 8 ? $horas - 1 : $horas;
                                $pago_real = ($employe->remuneracion / 8) * ( $horas >= 8 ? 8 : $horas );
                                // $pago_real = $pago_horas * ;
                                $horas_extras = $horas > 8 ? $horas - 8 : 0;
                                // $horas_25 = $horas_extras > 2 ? 2 : $horas_extras;
                                // $horas_35 = $horas_extras >= 3 ? $horas_extras - 2 : 0;
                                $horas_25 = $h_25 = $horas_extras > 2 ? 2 : $horas_extras;//acumula
                                $horas_35 = $horas_extras > 2 ? $horas_extras - $h_25: 0;
                                $prima = $horas_35 > 1 ?$employe->remuneracion/8 * 1.35 * ($horas_35 - 1):0;
                                $newregister->prima_produccion = $prima;
                                $remu = round(($pago_real + ($employe->remuneracion/8 * 1.25 * $horas_25) + ($employe->remuneracion/8 * 1.35 * $horas_35) +  ($employe->remuneracion/6) + (21.7/6)) * 1.1434,2);
                                if($fecha1->dayOfWeek == Carbon::SUNDAY){
                                    $newregister->paga = round($remu *2,2);
                                }else{
                                    $newregister->paga = round($remu,2);
                                }
                                // $javas_pas->paga = round(($employe->remuneracion + ($employe->remuneracion/8 * 1.25 * $horas_25) + ($employe->remuneracion/8 * 1.35 * $horas_35) +  ($employe->remuneracion/6) + (21.7/6)) * 1.1434,2);
                                $newregister->created_at = $fecha1->toDateTimeString();
                                $newregister->deleted_at = $fecha2->toDateTimeString();
                                // $newregister->synchronized_users = "[]";
                                $newregister->save();
                            }else{
                                $result['title'] = "ERROR";
                                $result['icon'] = "error";
                                $result['message'] = "Error fecha inicial mayor a la fecha de cierre.";
                            }
                        }
                        // $newregister->save();
                        // $success.= $current . ",";
                    }else{
                        $result['title'] = "ERROR";
                        $result['icon'] = "error";
                        $result['message'] = "Error , no se pudo registrar.";
                    }
                }else{
                    // no se registra nada
                    $result['title'] = "ERROR";
                    $result['icon'] = "error";
                    $result['message'] = "Error , no se pudo registrar nada.";
                }
            }

            $caledardata = Asistencia::where('id_employe','=',$id)
                ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
                DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
                'aux_type_reg.color as borderColor',
                'reg_assistance.created_at as start',
                'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

            $result['calendardata'] = json_encode($caledardata);

        } catch (\Throwable $th) {
            //throw $th;
            $result['title'] = "Error";
            $result['icon'] = "error";
            $result['message'] = $th->getMessage();
        }

        return response()->json($result, 200, []);
    }

    public function remove(Request $req,$id){
        //->forceDelete()
        //need decripcion

        $result = array();
        // return $req->all();
        try {
            if(!Auth::user()->hasGroupPermission("deleteHA")){
                // return abort('pages.403');
                // return response()->view('pages.403', [], 403);
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                $caledardata = Asistencia::where('id_employe','=',$id)
                ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
                DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
                'aux_type_reg.color as borderColor',
                'reg_assistance.created_at as start',
                'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

                $data['calendardata'] = json_encode($caledardata);
                return response()->json($data, 200, []);
            }
            //code...
            $fecha1 = Carbon::parse($req->d_since_at);
            $fecha2 = Carbon::parse($req->d_until_at)->addHours(23)->addMinutes(59);

            $delete = Asistencia::where('id_employe','=',$id)->where('id_aux_treg','=',$req->description)->whereBetween(DB::raw('UNIX_TIMESTAMP(created_at)'),[strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))])->delete();
            DB::table('reg_assistance')->where('id_employe','=',$id)->where('id_aux_treg','=',$req->description)->whereBetween(DB::raw('UNIX_TIMESTAMP(created_at)'),[strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))])
            ->update(array('synchronized_users' => "[]"));

            $result['data'] = $delete;

            $caledardata = Asistencia::where('id_employe','=',$id)
            ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
            DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
            'aux_type_reg.color as borderColor',
            'reg_assistance.created_at as start',
            'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

            $result['calendardata'] = json_encode($caledardata);
            $result['title'] = "Correcto";
            $result['icon'] = "success";
            $result['message'] = "Removido satisfactoriamente";
        } catch (\Throwable $th) {
            //throw $th;
            $result['title'] = "Error";
            $result['icon'] = "error";
            $result['message'] = $th->getMessage();
        }

        return response()->json($result, 200, []);
    }

    public function editRegister(Request $req,$id){
        $data = array();
        $headers = array();
        try {
            //code...
            $datos = Asistencia::withTrashed()
            ->select('reg_assistance.*',
            DB::raw('DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d") as d_since_at'),
            DB::raw('DATE_FORMAT(reg_assistance.deleted_at, "%Y-%m-%d") as d_until_at'),
            DB::raw('DATE_FORMAT(reg_assistance.deleted_at, "%H:%i") as h_until_at'),
            DB::raw('DATE_FORMAT(reg_assistance.created_at, "%H:%i") as h_since_at')
            )->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->where('reg_assistance.id','=',$id)->first();
            $datos->funcion->areas;
            if($req->all()){
                if(!Auth::user()->hasGroupPermission("updateHA")){
                    // return abort('pages.403');
                    // return response()->view('pages.403', [], 403);
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    $caledardata = Asistencia::where('id_employe','=',$datos->id_employe)
                    ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
                        DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
                    'aux_type_reg.color as borderColor',
                    'reg_assistance.created_at as start',
                    'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

                    $data['calendardata'] = json_encode($caledardata);
                    return response()->json($data, 200, []);
                }
                $datos = Asistencia::where('id','=',$id)->first();
                $registros_descuento = Asistencia::where('reg_assistance.id_employe',$datos->id_employe)
                ->where('id_aux_treg',3)
                ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime(Carbon::parse($datos->created_at)->setHours(0)->setMinutes(0)->subDay()->toDateTimeString()), strtotime(Carbon::parse($datos->deleted)->setHours(23)->setMinutes(59)->toDateTimeString())])
                ->get();
                $minutos_desc = 0;
                // dd($registros_descuento);
                foreach ($registros_descuento as $key => $value) {
                    # code...
                    $minutos_desc += Carbon::parse($value->created_at)->diffInMinutes(Carbon::parse($value->deleted_at));
                }
                // dd($minutos_desc);
                $datos->synchronized_users = "[]";
                // $datos->id_function = $employe->function;
                // $newregister->id_sede = $employe->id_sede;
                if($req->d_since_at && $req->d_until_at){
                    $ingreso = Carbon::parse($req->d_since_at . " " . $req->h_since_at)->timezone('America/Lima');// hora de ingreso 18:00:00
                    $salida = Carbon::parse($req->d_until_at . " " . $req->h_until_at)->timezone('America/Lima');// hora de salida 02:00:00
                    $mins = $ingreso->diffInMinutes($salida, true);
                    // $horas = $ingreso->diffinHours($salida);
                    $mins = $mins - $minutos_desc;
                    $horas = $mins/60;
                    $basico = $datos->employes->remuneracion;
                    $datos->basico = $basico;
                    // $datos->paga =
                    $horas = $horas > 8 ? $horas - 1 : $horas;
                    $pago_real = ($basico / 8) * ( $horas >= 8 ? 8 : $horas );
                    $horas_extras = $horas > 8 ? $horas - 8 : 0;
                    // $horas_25 = $horas_extras > 2 ? 2 : $horas_extras;
                    // $horas_35 = $horas_extras >= 3 ? $horas_extras - 2 : 0;
                    $horas_25 = $h_25 = $horas_extras > 2 ? 2 : $horas_extras;//acumula
                    $horas_35 = $horas_extras > 2 ? $horas_extras - $h_25: 0;
                    $prima = $horas_35 > 1 ?$basico/8 * 1.35 * ($horas_35 - 1):0;
                    $remu = round(($pago_real + ($basico/8 * 1.25 * $horas_25) + ($basico/8 * 1.35 * $horas_35) +  ($basico/6) + (21.7/6)) * 1.1434,2);
                    $datos->prima_produccion = $prima;
                    $morning = Carbon::create($ingreso->year, $ingreso->month, $ingreso->day, 4, 0, 0); //set time to 04:00 : 4AM
                    $evening = Carbon::create($ingreso->year, $ingreso->month, $ingreso->day, 14, 0, 0); //set time to 14:00 : 2PM
                    if($ingreso->between($morning, $evening, true)) {
                        //current time is between morning and evening
                        $datos->turno = "DIA";
                    } else {
                        $datos->turno = "NOCHE";
                        //current time is earlier than morning or later than evening
                    }
                    if($ingreso->dayOfWeek == Carbon::SUNDAY){
                        $datos->paga = round($remu *2,2);
                    }else{
                        $datos->paga = round($remu,2);
                    }
                    $datos->created_at = Carbon::parse($req->d_since_at)->addHour(Carbon::parse($req->h_since_at)->format('H'))->addMinutes(Carbon::parse($req->h_since_at)->format('i'));
                    $datos->deleted_at = Carbon::parse($req->d_until_at)->addHour(Carbon::parse($req->h_until_at)->format('H'))->addMinutes(Carbon::parse($req->h_until_at)->format('i'));

                    $reg_un = DB::table('reg_unchecked')->where('reg_code',$datos->employes->code)->whereDate('created_at',$ingreso->toDateString())->first();

                    if($reg_un){
                        $datos->id_user_checked = $reg_un->id_user;
                        $datos->checked_at = $reg_un->created_at;
                        $datos->checked = 1;
                        $datos->synchronized_users = "[]";
                        $datos->save();
                    }

                    DB::table('reg_unchecked')->where('reg_code',$datos->employes->code)->whereDate('created_at',$ingreso->toDateString())->delete();
                    DB::table('reg_employes')->where('code',$datos->employes->code)->whereDate('created_at',$ingreso->toDateString())->delete();
                }

                if(empty($req->d_since_at) && $req->d_until_at){
                    $datos->turno = "S/T";
                    $datos->basico = $datos->employes->remuneracion;
                    $datos->paga = $datos->employes->remuneracion;
                    $datos->prima_produccion = 0;
                    $datos->created_at = null;
                    $datos->deleted_at = Carbon::parse($req->d_until_at)->addHour(Carbon::parse($req->h_until_at)->format('H'))->addMinutes(Carbon::parse($req->h_until_at)->format('i'));
                }

                if($req->d_since_at && empty($req->d_until_at)){
                    $ingreso = Carbon::parse($req->d_since_at . " " . $req->h_since_at)->timezone('America/Lima');// hora de ingreso 18:00:00
                    $morning = Carbon::create($ingreso->year, $ingreso->month, $ingreso->day, 4, 0, 0); //set time to 04:00 : 4AM
                    $evening = Carbon::create($ingreso->year, $ingreso->month, $ingreso->day, 14, 0, 0); //set time to 14:00 : 2PM
                    if($ingreso->between($morning, $evening, true)) {
                        //current time is between morning and evening
                        $datos->turno = "DIA";
                    } else {
                        $datos->turno = "NOCHE";
                        //current time is earlier than morning or later than evening
                    }
                    $basico =$datos->employes->remuneracion;
                    $datos->basico = $basico;
                    $datos->prima_produccion = 0;
                    $remu = ($basico +  ($basico/6) + (21.7/6)) * 1.1434;
                    if($ingreso->dayOfWeek == Carbon::SUNDAY){
                        $datos->paga = round($remu *2,2);
                    }else{
                        $datos->paga = round($remu,2);
                    }//calculo con horas jornales
                    $datos->created_at = Carbon::parse($req->d_since_at)->addHour(Carbon::parse($req->h_since_at)->format('H'))->addMinutes(Carbon::parse($req->h_since_at)->format('i'));
                    $datos->deleted_at = null;
                    $reg_un = DB::table('reg_unchecked')->where('reg_code',$datos->employes->code)->whereDate('created_at',$ingreso->toDateString())->first();

                    if($reg_un){
                        $datos->id_user_checked = $reg_un->id_user;
                        $datos->checked_at = $reg_un->created_at;
                        $datos->checked = 1;
                        $datos->synchronized_users = "[]";
                        $datos->save();
                    }

                    DB::table('reg_unchecked')->where('reg_code',$datos->employes->code)->whereDate('created_at',$ingreso->toDateString())->delete();
                    DB::table('reg_employes')->where('code',$datos->employes->code)->whereDate('created_at',$ingreso->toDateString())->delete();
                }

                $datos->save();
                // return $req->all();
                $caledardata = Asistencia::where('id_employe','=',$datos->id_employe)->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
                DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
                'aux_type_reg.color as borderColor',
                'reg_assistance.created_at as start',
                'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();
                $data['calendardata'] = json_encode($caledardata);
                $data['success'] = true;
                $data['title'] = "Correcto";
                $data['icon'] = "success";
                $data['message'] = "Editado satisfactoriamente";
                // return response()->json($data, 200, $headers);
            }else{
                $data['data'] = $datos;
                $data['success'] = true;

            }
        } catch (\Throwable $th) {
            //throw $th;
            $data['title'] = "Error";
            $data['icon'] = "error";
            $data['message'] = $th->getMessage();
        }
        return response()->json($data, 200, $headers);
    }

    public function editValidacion(Request $req,$id){
        try {
            //code...
            if(!Auth::user()->hasGroupPermission("updateHA")){
                // return abort('pages.403');
                // return response()->view('pages.403', [], 403);
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            $datos = Asistencia::where('id','=',$id)->first();
            $datos->id_aux_treg = 1;
            $datos->synchronized_users = "[]";
            // $datos->id_function = $employe->function;
            // $newregister->id_sede = $employe->id_sede;
            // dd($req->all());
            $datos->id_function = $req->id_funcion;
            $datos->id_proceso = $req->id_proceso;
            $datos->c_costo = $req->c_costo;
            $datos->dir_ind = $req->dir_ind;
            $datos->type = $req->des_jor;
            $datos->save();
            // dd($req->permanent);
            if($req->permanent == "true"){
                // dd("ok");
                $employe = Employes::find($datos->id_employe);
                $employe->id_proceso = $req->id_proceso;
                $employe->id_function = $req->id_funcion;
                $employe->dir_ind = $req->dir_ind;
                $employe->type = $req->des_jor;
                $employe->c_costo = $req->c_costo;
                $employe->save();
            }

            $data['success'] = true;
            $data['title'] = "Correcto";
            $data['icon'] = "success";
            $data['message'] = "Editado satisfactoriamente";
        } catch (\Throwable $th) {
            //throw $th;
            $data['title'] = "Error";
            $data['icon'] = "error";
            $data['message'] = $th->getMessage();
        }
        return response()->json($data, 200, []);
    }

    public function massive(){
        if(!Auth::user()->hasGroupPermission("viewHA")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        $areas = Area::all();
        return view('assistance.massive.index',compact('areas'));
    }

    public function getregister($code){
        $data = array();
        $headers = array();
        $codigo = $code;
        $datos = Asistencia::whereHas('employes',function($query) use ($codigo){
            $query->where('code','=',$codigo);
        })->orderBy('created_at','desc')->limit(50)->get();

        foreach ($datos as $key => $value) {
            # code...

            $data['data'][$key] = [
                $key + 1,
                Carbon::parse($value->created_at)->format('d/m/Y H:i:s'),
                Carbon::parse($value->deleted_at)->format('d/m/Y H:i:s'),
                $value->aux_type->description
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function massiveReg(Request $req){
        $datos = array();
        //replace
        try {
            //code...
            $fecha1 = Carbon::parse($req->d_since_at);
            $fecha2 = Carbon::parse($req->d_until_at);

            $since_h = Carbon::parse($req->h_since_at);
            $until_h = Carbon::parse($req->h_until_at);

            $days = $fecha1->diffInDays($fecha2) + 1; // count days bettwen fecha1 and fecha2, adding a day

            if($req->type == 1){
                //registro
                if(!Auth::user()->hasGroupPermission("createHA")){
                    // return abort('pages.403');
                    // return response()->view('pages.403', [], 403);
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                $aux_treg = AuxiliarTypeReg::where('id','=',$req->description)->first();
                foreach ($req->ids as $key => $value) {
                    # code...
                    $employe = Employes::where('code','=',$value)->first();
                    $success = "";
                    $failed = "";
                    for ($i = 0; $i < $days; $i++) {
                        # code...
                        $fecha1 = Carbon::parse($req->d_since_at)->addHours($since_h->format('H'))->addMinutes($since_h->format('i'));
                        $fecha2 = Carbon::parse($req->d_since_at)->addHours($until_h->format('H'))->addMinutes($until_h->format('i'));
                        $current = $fecha1->addDays($i);
                        $current2 = $fecha2->addDays($i);
                        $asistencia = Asistencia::where('id_employe', '=', $employe->id)->whereDate('created_at','=',$current)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->first();
                        // dd($asistencia);
                        if(!$asistencia){
                            $newregister = new Asistencia();
                            $newregister->id_aux_treg = $req->description;
                            $newregister->id_employe = $employe->id;
                            $newregister->synchronized_users = "[]";
                            $newregister->id_function = $employe->id_function;
                            $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                            $newregister->id_proceso = $employe->id_proceso;
                            $newregister->basico = $employe->remuneracion;
                            $newregister->c_costo = $employe->c_costo;
                            // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                            $newregister->created_at = $current;
                            if($req->d_until_at){
                                $newregister->deleted_at = $current2;
                            }
                            $newregister->save();
                            $datos['data'][$i] = $newregister;
                            $success .= $current . ",";
                        }else{
                            //si es asistencia
                            if($asistencia->id_aux_treg == 1 || $asistencia->aux_type->aditionable == 1){
                                //registra adicional
                                //no se puede a���adir dos asistencias seguidas
                                    if($aux_treg->id != $asistencia->id_aux_treg){
                                        if($aux_treg->aditionable == 0 && $aux_treg->id != 1){
                                            Asistencia::where('id_employe', '=', $employe->id)->whereDate('created_at','=',$current)->orderBy('id', 'desc')->orderBy('created_at', 'desc')->delete();
                                        }
                                        $newregister = new Asistencia();
                                        $newregister->id_aux_treg = $req->description;
                                        $newregister->id_employe = $employe->id;
                                        $newregister->synchronized_users = "[]";
                                        $newregister->id_function = $employe->id_function;
                                        $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                                        $newregister->id_proceso = $employe->id_proceso;
                                        $newregister->c_costo = $employe->c_costo;
                                        // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                                        $newregister->created_at = $current;
                                        if($req->d_until_at){
                                            $newregister->deleted_at = $current2;
                                        }
                                        $newregister->save();
                                        $success.= $current . ",";
                                    }else{
                                        $failed .= $current . ",";
                                    }
                            }else{
                                // no se registra nada
                                $failed .= $current . ",";
                            }
                        }
                    }
                }
                $datos['title'] = "Registros";
                $datos['icon'] = "info";
                $datos['message'] = "Registrado satisfactoriamente :". $success."\n".
                                    "Fallido : ".$failed;
            }else{
                if(!Auth::user()->hasGroupPermission("deleteHA")){
                    // return abort('pages.403');
                    // return response()->view('pages.403', [], 403);
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                //remuevo
                foreach ($req->ids as $key => $value) {
                    # code...
                    $employe = Employes::where('code','=',$value)->first();
                    $data = Asistencia::where('id_aux_treg','=',$req->description)->where('id_employe','=',$employe->id)->whereBetween(DB::raw('UNIX_TIMESTAMP(created_at)'),[strtotime(Carbon::parse($req->d_since_at)->format('Y-m-d H:i')),strtotime(Carbon::parse($req->d_until_at)->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])->delete();
                    DB::table('reg_assistance')->where('id_aux_treg','=',$req->description)->where('id_employe','=',$employe->id)->whereBetween(DB::raw('UNIX_TIMESTAMP(created_at)'),[strtotime(Carbon::parse($req->d_since_at)->format('Y-m-d H:i')),strtotime(Carbon::parse($req->d_until_at)->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])
                    ->update(array('synchronized_users' => "[]"));
                }
                $datos['title'] = "Correcto";
                $datos['icon'] = "success";
                $datos['message'] = "Removido satisfactoriamente";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $datos['title'] = "Error";
            $datos['icon'] = "error";
            $datos['message'] = $th->getMessage();
        }
        // $datos['mov'] = $req->type;
        // $datos['message'] = view('assistance.massive.massiveresponse',['tipo' => $req->type])->render();//obsoleto

        return response()->json($datos, 200, []);
    }

    public function delete($id){
        try {
            $datos = Asistencia::withTrashed()
            ->select('reg_assistance.*',
            DB::raw('DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d") as d_since_at'),
            DB::raw('DATE_FORMAT(reg_assistance.deleted_at, "%Y-%m-%d") as d_until_at'),
            DB::raw('DATE_FORMAT(reg_assistance.deleted_at, "%H:%i") as h_until_at'),
            DB::raw('DATE_FORMAT(reg_assistance.created_at, "%H:%i") as h_since_at')
            )->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->where('reg_assistance.id','=',$id)->first();

            Asistencia::withTrashed()->where('reg_assistance.id','=',$id)->forceDelete();

            $caledardata = Asistencia::where('id_employe','=',$datos->id_employe)
            ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
            DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
            'aux_type_reg.color as borderColor',
            'reg_assistance.created_at as start',
            'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

            $result['calendardata'] = json_encode($caledardata);
            $result['success'] = true;
            $result['title'] = "Correcto";
            $result['icon'] = "success";
            $result['message'] = "Editado satisfactoriamente";
        } catch (\Throwable $th) {
            $result['title'] = "Error";
            $result['icon'] = "error";
            $result['message'] = $th->getMessage();
        }
        return response()->json($result, 200, []);
    }
    //new functions
    public function reg_assistance(Request $req,$employe_id){
        Carbon::setLocale('es_PE');
        $time = Carbon::now()->timezone('America/Lima');
        $morning = Carbon::create($time->year, $time->month, $time->day, 4, 0, 0); //set time to 04:00 : 4AM
        $evening = Carbon::create($time->year, $time->month, $time->day, 14, 0, 0); //set time to 14:00 : 2PM

        try {
            if(!Auth::user()->hasGroupPermission("createHA")){
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                $caledardata = Asistencia::where('id_employe','=',$employe_id)
                ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
                DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
                'aux_type_reg.color as borderColor',
                'reg_assistance.created_at as start',
                'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

                $data['calendardata'] = json_encode($caledardata);
                return response()->json($data, 200, []);
            }

            $result = array();
            $result['title'] = "Incorrecto";
            $result['icon'] = "warning";
            $result['message'] = "Existe un registro que impide la asistencia.";

            $since_h = Carbon::parse($req->h_since_at);//hora de registro
            $until_h = Carbon::parse($req->h_until_at);//hora de termino/salida

            $employe = Employes::findOrFail($employe_id);//datos del trabajador

            $fecha1 = Carbon::parse($req->d_since_at)->setHours($since_h->format('H'))->setMinutes($since_h->format('i'));//formatdo de fecha ingreso/registro
            $fecha2 = Carbon::parse($req->d_until_at)->setHours($until_h->format('H'))->setMinutes($until_h->format('i'));//formato de fecha de salida/termino

            //$asistencia = Asistencia::where('id_employe', '=', $employe->id)->whereDate('created_at','=',$fecha1->toDateString())->orderBy('created_at', 'desc')->first();
            $registro = Asistencia::where('id_employe','=',$employe->id)
            ->whereDate('created_at_search','<=',$fecha1->toDateString())
            ->whereNull('deletedAt')
            ->orderBy('created_at_search','desc')
            ->orderBy('created_at','desc')
            ->first();//registro
            // el registro de asistencia procede siempre y cuando exista un registro de falta en el dia
            if(!$registro){
                // registra asistencia
                $result = $this->asistencia($req,$employe,$fecha1,$fecha2,$morning,$evening);
            }else{
                if($registro->created_at && $registro->deleted_at || $registro->id_aux_treg == 10){// 10 = salida
                    if(Carbon::parse($registro->deleted_at)->lte($fecha1)){
                        // si la fecha del registro de salida encontrado es menor o igual al que se quiere registrar
                        $result = $this->asistencia($req,$employe,$fecha1,$fecha2,$morning,$evening);
                    }
                }
                // si la fecha de termino no existe
                if($registro->created_at && is_null($registro->deleted_at)){
                    //fecha de inicio menor o igual a la fecha de registro
                    if(Carbon::parse($registro->created_at)->lt($fecha1)){
                        $result = $this->asistencia($req,$employe,$fecha1,$fecha2,$morning,$evening);
                    }
                }
                // si la fecha de inicio no existe
                if(is_null($registro->created_at) && $registro->deleted_at){
                    // fecha de salida es menor o igual a la fecha de registro
                    if(Carbon::parse($registro->deleted_at)->lte($fecha1)){
                        $result = $this->asistencia($req,$employe,$fecha1,$fecha2,$morning,$evening);
                    }
                }
            }

            $caledardata = Asistencia::where('id_employe','=',$employe_id)
            ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
            DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
            'aux_type_reg.color as borderColor',
            'reg_assistance.created_at as start',
            'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

            $result['calendardata'] = json_encode($caledardata);

        } catch (\Throwable $th) {
            //throw $th;
            if($th->getCode() == 23000) //Violation integrity
            {
                $result['title'] = "Error";
                $result['icon'] = "error";
                $result['message'] = "Duplicado";
                return response()->json($result, 200, []);
            }
            $result['title'] = "Error";
            $result['icon'] = "error";
            $result['message'] = $th->getMessage();
        }

        return response()->json($result,200,[]);
    }

    public function reg_permission(Request $req,$employe_id){
        //search asistance
        Carbon::setLocale('es');
        $time = Carbon::now()->timezone('America/Lima');
        $resolution = CarbonInterval::minutes();
        $morning = Carbon::create($time->year, $time->month, $time->day, 4, 0, 0); //set time to 04:00 : 4AM
        $evening = Carbon::create($time->year, $time->month, $time->day, 14, 0, 0); //set time to 14:00 : 2PM
        $result = array();
        //verificar si el permiso es pagado
        try {
            //code...
            if(!Auth::user()->hasGroupPermission("createHA")){
                // return abort('pages.403');
                // return response()->view('pages.403', [], 403);
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                $caledardata = Asistencia::where('id_employe','=',$employe_id)
                ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
                DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
                'aux_type_reg.color as borderColor',
                'reg_assistance.created_at as start',
                'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

                $data['calendardata'] = json_encode($caledardata);
                return response()->json($data, 200, []);
            }
            $since_h = Carbon::parse($req->since_hour_at);
            $until_h = Carbon::parse($req->since_hour_until);

            $fecha = Carbon::parse($req->since_date);
            $employe = Employes::findOrFail($employe_id);
            $inicio_hora_desc =  Carbon::parse($req->since_date)->setHours($since_h->format('H'))->setMinutes($since_h->format('i'));
            $fin_hora_desc =  Carbon::parse($req->since_date)->setHours($until_h->format('H'))->setMinutes($until_h->format('i'));
            $asistencia = Asistencia::where('id_employe', $employe_id)->whereDate('created_at',$fecha->toDateString())->orderBy('created_at', 'desc')->first();
            $permisos = Asistencia::where('id_employe', $employe_id)->where('id_aux_treg',3)->whereDate('created_at_search',$fecha->toDateString())->orderBy('created_at', 'desc')->get();

            $result['title'] = "Registro correcto";
            $result['icon'] = "success";
            $result['message'] = "Permiso registrado satisfactorio;";
            if($asistencia){
                if(count($permisos) > 0){
                    $result['title'] = "Permiso ya existente";
                    $result['icon'] = "error";
                    $result['message'] = "Ya registra un permiso";
                }else
                    if($asistencia->id_aux_treg == 1 && $asistencia->created_at && $asistencia->deleted_at ){
                        if($req->since_hour_at && $req->since_hour_until){
                            //comprobar que el registro de permiso este dentro de la hora de asistencia.
                            if($fin_hora_desc->gt($inicio_hora_desc)){
                                $horas_desc = 0;
                                //si existe horas entonces las añade
                                if($asistencia->horas_descontadas != "00:00:00"){
                                    $timeparts = explode(':', $asistencia->horas_descontadas);
                                    $horas_desc += ($timeparts[0]*60) + ($timeparts[1]) + ($timeparts[2]/60);
                                }
                                //añade horas horas del formulario
                                $horas_desc += $inicio_hora_desc->diffInMinutes($fin_hora_desc) / 60;
                                //añade formato a las horas decimales
                                $asistencia->horas_descontadas = sprintf('%02d:%02d:00', (int) $horas_desc, fmod($horas_desc, 1) * 60);
                                //descuenta horas al total de horas registrada en la asistencia y recalcula
                                $horas = $asistencia->created_at->diffInMinutes($asistencia->deleted_at, true) / 60;
                                $horas = $horas - $horas_desc;
                                //verifica si existe pago al 100%
                                if($asistencia->paga_100>0){
                                    $pago_real = (($asistencia->basico / 8)* 2) * $horas;//obtiene el basico del dia asistido
                                    $asistencia->paga_100 = round($pago_real *2,2);
                                    $asistencia->horas_100 = $horas;
                                }else{
                                    $horas_nocturna = 0;
                                    $horas = $horas > 8 ? $horas - $asistencia->hasDinner : $horas;

                                    $pago_real = ($asistencia->basico / 8) * ( $horas >= 8 ? 8 : $horas );
                                    $horas_extras = $horas > 8 ? $horas - 8 : 0;

                                    $horas_25 = $h_25 = $horas_extras > 2 ? 2 : $horas_extras;//acumula
                                    $horas_35 = $horas_extras > 2? $horas_extras - $h_25: 0;

                                    $paga_25 = $asistencia->basico/ 8 * 1.25 * $horas_25;
                                    $paga_35 = $asistencia->basico/ 8 * 1.35 * $horas_35;

                                    $prima = $horas_35 > 1 ?$asistencia->basico/ 8 * 1.35 * ($horas_35 - 1):0;
                                    $created_at = Carbon::parse($asistencia->created_at);
                                    $deleted_at = Carbon::parse($asistencia->deleted_at);
                                    // print($inicio->format('Y-m-d'));
                                    $inicio_limite = Carbon::create($created_at->year, $created_at->month, $created_at->day, 22, 0, 0); //set time to 22:00 : 10PM
                                    $final_limite = Carbon::create($deleted_at->year, $deleted_at->month, $deleted_at->day, 6, 0, 0); //set time to 06:00 : 6AM
                                    $minutes = 0;

                                    if($inicio_limite->between($created_at,$deleted_at,true) || $final_limite->between($created_at,$deleted_at,true)) {
                                        // check if 10PM is between start and end date equal in 6AM

                                        try {
                                                $minutes = $created_at->diffFiltered($resolution, function (Carbon $date) {
                                                    //print($date->toDateTimeString());
                                                    //$this->exception_text = $date->hour . ":" . $date->minute. "\n";
                                                    //$this->exception = $date->toDateTimeString();
                                                    return ($date->hour <= 6 && $date->hour >= 0 ) || ($date->hour >= 22 && $date->hour <= 23);
                                                }, $deleted_at);
                                                //exit;
                                        } catch (\Exception $e) {
                                            //print($fecha1->format('Y-m-d H:i:s') . " " . $fecha2->format('Y-m-d H:i:s'));
                                            //print($this->exception);
                                            //dd($e);
                                            exit;
                                        }
                                        $horas_nocturna = $minutes/60;
                                    }
                                    //dd(($employe->remuneracion/6) + (21.7/6));
                                    $paga_nocturna = $employe->remuneracion/ 8 * 1.35 * $horas_nocturna;
                                    $pago_descanso = $employe->remuneracion/6;
                                    $pago_bono_familiar = 21.7/6;
                                    $remu = round(
                                        ($pago_real +
                                            $paga_25 +
                                            $paga_35 +
                                            $paga_nocturna +
                                            $pago_descanso +
                                        $pago_bono_familiar) *
                                    1.1434 ,2);

                                    $asistencia->paga = round($remu,2);

                                    $asistencia->horas_trabajadas = sprintf('%02d:%02d:00', (int) $horas, fmod($horas, 1) * 60);
                                    $asistencia->paga_25 = $paga_25;
                                    $asistencia->horas_25 = sprintf('%02d:%02d:00', (int) $horas_25, fmod($horas_25, 1) * 60);
                                    $asistencia->paga_35 = $paga_35;
                                    $asistencia->horas_35 = sprintf('%02d:%02d:00', (int) $horas_35, fmod($horas_35, 1) * 60);
                                    $asistencia->prima_produccion = $prima;
                                    $asistencia->horas_prima_produccion =  $horas_35 > 1 ? sprintf('%02d:%02d:00', (int) $horas_35-1, fmod($horas_35-1, 1) * 60):'00:00:00';
                                    $asistencia->paga_nocturna = $paga_nocturna;
                                    $asistencia->horas_nocturna = sprintf('%02d:%02d:00', (int) $horas_nocturna, fmod($horas_nocturna, 1) * 60);
                                    $asistencia->paga_descanso = $pago_descanso;
                                    $asistencia->paga_bono_familiar = $pago_bono_familiar;
                                }
                                $asistencia->save();
                                $turno = $inicio_hora_desc->between($morning, $evening, true) ? "DIA":"NOCHE";
                                //registrar permiso
                                $newregister = new Asistencia();
                                $newregister->id_aux_treg = 3;// permiso
                                $newregister->id_employe = $employe_id;
                                $newregister->synchronized_users = "[]";
                                $newregister->id_function = $employe->id_function;
                                $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                                // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                                $newregister->id_employe_type = $employe->id_employe_type;
                                $newregister->dir_ind = $employe->dir_ind;
                                $newregister->type = $employe->type;
                                $newregister->id_proceso = $employe->id_proceso;
                                $newregister->basico = $employe->remuneracion;
                                $newregister->c_costo = $employe->c_costo;
                                $newregister->created_at = $inicio_hora_desc->toDateTimeString();
                                $newregister->deleted_at = $fin_hora_desc->toDateTimeString();
                                $newregister->created_at_search = $inicio_hora_desc->toDateString();
                                $newregister->turno = $turno;
                                $newregister->uniqueReg = $inicio_hora_desc->toDateString().".3.".$employe_id.".".$turno;
                                $newregister->save();
                            }else{
                                $result['title'] = "Error";
                                $result['icon'] = "error";
                                $result['message'] = "Hora de salida mayor a la de retorno";
                                //mensaje de hora de salida mayor que hora de retorno
                            }
                        }elseif($req->since_hour_at && !$req->since_hour_until){

                            //comprobar que la hora de salida sea mayor o igual que la marca de salida de asistencia
                            $salida_limite = Carbon::create($asistencia->deleted_at); //

                            if($inicio_hora_desc->gte($salida_limite)) {
                                //salida sin retorno
                                $turno = $inicio_hora_desc->between($morning, $evening, true) ? "DIA":"NOCHE";
                                $newregister = new Asistencia();
                                $newregister->id_aux_treg = 3;// permiso
                                $newregister->id_employe = $employe_id;
                                $newregister->synchronized_users = "[]";
                                $newregister->id_function = $employe->id_function;
                                $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                                // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                                $newregister->id_employe_type = $employe->id_employe_type;
                                $newregister->dir_ind = $employe->dir_ind;
                                $newregister->type = $employe->type;
                                $newregister->id_proceso = $employe->id_proceso;
                                $newregister->basico = $employe->remuneracion;
                                $newregister->c_costo = $employe->c_costo;
                                $newregister->created_at = $inicio_hora_desc->toDateTimeString();
                                $newregister->turno = $turno;
                                $newregister->created_at_search = $inicio_hora_desc->toDateString();
                                $newregister->uniqueReg = $inicio_hora_desc->toDateString().".3.".$employe_id.".".$turno;
                                $newregister->save();
                            }else{
                                $result['title'] = "Error";
                                $result['icon'] = "error";
                                $result['message'] = "La hora de salida del permiso es menor que la salida de la asistencia.";
                            }
                        }else{
                            $result['title'] = "Registro incorrecto";
                            $result['icon'] = "error";
                            $result['message'] = "Completa los campos correctamente;";
                        }
                    }else{
                        $result['title'] = "Permiso Incorrecto";
                        $result['icon'] = "error";
                        $result['message'] = "Asistencia existente no corresponde.";
                    }
                //solo registra un permiso si existe una asistencia
            }else{
                $result['title'] = "No registra asistencia";
                $result['icon'] = "error";
                $result['message'] = "No registra ninguna asistencia";
            }

            $caledardata = Asistencia::where('id_employe','=',$employe_id)
            ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
            DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
            'aux_type_reg.color as borderColor',
            'reg_assistance.created_at as start',
            'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

            $result['calendardata'] = json_encode($caledardata);


        } catch (\Throwable $th) {
            //throw $th;
            $result['title'] = "Error";
            $result['icon'] = "error";
            $result['message'] = $th->getMessage();
        }
        return response()->json($result,200,[]);
    }

    public function reg_licence(Request $req,$employe_id){
        Carbon::setLocale('es');
        $result = array();
        try {
            //code...
            $employe = Employes::findOrFail($employe_id);
            $fecha1 = Carbon::parse($req->d_since_at);
            $fecha2 = Carbon::parse($req->d_until_at)->addHours(23)->addMinutes(59)->addSeconds(59);
            /*
            //buscamos el ultimo registro registrada
            $last_register = Asistencia::whereNotNull('created_at')->orderBy('deleted_at','desc')->first();
            //comparamos la fecha de inicio y fin
            $inicio = Carbon::create($last_register->created_at); //set time to 04:00 : 4AM
            $final = Carbon::create($last_register->deleted_at); //set time to 14:00 : 2PM

            if($inicio->between($fecha1,$fecha2,true) || $final->between($fecha1,$fecha2,true)) {
                //elimina todos los registros que esten registrados en el rango de esta fecha
            }*/
            if($req->d_until_at && $req->d_since_at){
                if($fecha2->gt($fecha1)){
                    Asistencia::whereBetween('created_at',[$fecha1->toDateString(),$fecha2->toDateString()])->delete();//elimna todos los registros encontrados

                    $licencia = new Asistencia();
                    $licencia->id_aux_treg = $req->description;// asistencia
                    $licencia->id_employe = $employe_id;
                    $licencia->synchronized_users = "[]";
                    $licencia->id_function = $employe->id_function;
                    $licencia->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                    // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                    $licencia->id_employe_type = $employe->id_employe_type;
                    $licencia->dir_ind = $employe->dir_ind;
                    $licencia->type = $employe->type;
                    $licencia->id_proceso = $employe->id_proceso;
                    $licencia->basico = $employe->remuneracion;
                    $licencia->c_costo = $employe->c_costo;
                    $licencia->created_at = $fecha1->toDateTimeString();
                    $licencia->deleted_at = $fecha2->toDateTimeString();
                    $licencia->created_at_search = $fecha1->toDateString();
                    $licencia->turno = "S/T";
                    $licencia->uniqueReg = $fecha1->toDateString().".".$req->description.".".$employe->id.".S/T";
                    $licencia->save();

                    $result['title'] = "Registrado";
                    $result['icon'] = "success";
                    $result['message'] = "Licencia registrada correctamente";
                }else{
                    $result['title'] = "Error";
                    $result['icon'] = "error";
                    $result['message'] = "Fecha de termino menor que la fecha de inicio.";
                }

            }else{
                $result['title'] = "Error Fecha";
                $result['icon'] = "error";
                $result['message'] = "Coloca las fechas correctamente.";
            }

            $caledardata = Asistencia::where('id_employe','=',$employe_id)
            ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
            DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
            'aux_type_reg.color as borderColor',
            'reg_assistance.created_at as start',
            'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

            $result['calendardata'] = json_encode($caledardata);


        } catch (\Throwable $th) {
            //throw $th;
            $result['title'] = "Error";
            $result['icon'] = "error";
            $result['message'] = $th->getMessage();
        }
        return response()->json($result,200,[]);
    }

    public function reg_cese(Request $req,$employe_id){
        //registra el cese del trabajador
        $result = array();
        $result['title'] = "Incorrecto";
        $result['icon'] = "warning";
        $result['message'] = "Existe un registro que impide el cese.";
        try{
            $fecha1 = Carbon::parse($req->d_since_at);
            $fecha2 = Carbon::parse($req->d_since_at)->addYears(1)->addHours(23)->addMinutes(59)->addSeconds(59);
            $employe = Employes::findOrFail($employe_id);
            //$asistencia = Asistencia::where('id_employe', '=', $employe->id)->whereDate('created_at','=',$fecha1->toDateString())->orderBy('created_at', 'desc')->first();
            $registros = Asistencia::where('id_employe','=',$employe->id)->whereDate('created_at','<=',$fecha1->toDateString())->orderBy('created_at','desc')->first();
            // el registro de asistencia procede siempre y cuando exista un registro de falta en el dia
            if(!$registros){
                $result['title'] = "Correcto";
                $result['icon'] = "success";
                $result['message'] = "CESE REGISTRADO.";
                Asistencia::where(function($query) use ($fecha1, $fecha2){
                    $query->whereBetween('created_at',[$fecha1->toDateString(),$fecha2->toDateString()])
                    ->orwhereBetween('deleted_at',[$fecha1->toDateString(),$fecha2->toDateString()]);
                  })
                ->delete();
                $newregister = new Asistencia();
                $newregister->id_aux_treg = 6;// cese
                $newregister->id_employe = $employe->id;
                $newregister->synchronized_users = "[]";
                $newregister->id_function = $employe->id_function;
                $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                $newregister->id_employe_type = $employe->id_employe_type;
                $newregister->dir_ind = $employe->dir_ind;
                $newregister->type = $employe->type;
                $newregister->id_proceso = $employe->id_proceso;
                $newregister->basico = $employe->remuneracion;
                $newregister->c_costo = $employe->c_costo;
                $newregister->created_at = $fecha1->toDateTimeString();
                $newregister->deleted_at = $fecha2->toDateTimeString();
                $newregister->created_at_search = $fecha1->toDateString();
                $newregister->turno = "S/T";
                $newregister->uniqueReg = $fecha1->toDateString().".6.".$employe->id.".S/T";
                $newregister->save();
                // elimina los demas registrs
            }else{
                if($fecha1->gte($registros->deleted_at)){
                    $result['title'] = "Correcto";
                    $result['icon'] = "success";
                    $result['message'] = "CESE REGISTRADO.";
                    Asistencia::where(function($query) use ($fecha1, $fecha2){
                        $query->whereBetween('created_at',[$fecha1->toDateString(),$fecha2->toDateString()])
                        ->orwhereBetween('deleted_at',[$fecha1->toDateString(),$fecha2->toDateString()]);
                      })
                    ->delete();
                    $newregister = new Asistencia();
                    $newregister->id_aux_treg = 6;// cese
                    $newregister->id_employe = $employe->id;
                    $newregister->synchronized_users = "[]";
                    $newregister->id_function = $employe->id_function;
                    $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                    // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                    $newregister->id_employe_type = $employe->id_employe_type;
                    $newregister->dir_ind = $employe->dir_ind;
                    $newregister->type = $employe->type;
                    $newregister->id_proceso = $employe->id_proceso;
                    $newregister->basico = $employe->remuneracion;
                    $newregister->c_costo = $employe->c_costo;
                    $newregister->created_at = $fecha1->toDateTimeString();
                    $newregister->deleted_at = $fecha2->toDateTimeString();
                    $newregister->turno = "S/T";
                    $newregister->uniqueReg = $fecha1->toDateString().".6.".$employe->id.".S/T";
                    $newregister->created_at_search = $fecha1->toDateString();
                    $newregister->save();
                    // elimina los demas registrs
                }
            }
            $caledardata = Asistencia::where('id_employe','=',$employe_id)
            ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
            DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
            'aux_type_reg.color as borderColor',
            'reg_assistance.created_at as start',
            'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

            $result['calendardata'] = json_encode($caledardata);
        } catch (\Throwable $th) {
            //throw $th;
            $result['title'] = "Error";
            $result['icon'] = "error";
            $result['message'] = $th->getMessage();
        }

        return response()->json($result,200,[]);
    }

    public function reg_vacation(Request $req,$employe_id){
        //registra las vacaiones del empleado max 30 dias
        $result = array();

        $result['title'] = "Incorrecto";
        $result['icon'] = "warning";
        $result['message'] = "Existe un registro que impide las vacaciones.";

        try {
            //code...
            $fecha1 = Carbon::parse($req->d_since_at);
            $fecha2 = Carbon::parse($req->d_until_at)->addHours(23)->addMinutes(59)->addSeconds(59);

            if($req->d_since_at && $req->d_until_at){
                if($fecha2->gt($fecha1)){
                    if($fecha1->diffInDays($fecha2) >= 30){
                        $employe = Employes::findOrFail($employe_id);
                        //$asistencia = Asistencia::where('id_employe', '=', $employe->id)->whereDate('created_at','=',$fecha1->toDateString())->orderBy('created_at', 'desc')->first();
                        $registros = Asistencia::where('id_employe','=',$employe->id)->whereDate('created_at','<=',$fecha1->toDateString())->orderBy('created_at','desc')->first();
                        // el registro de asistencia procede siempre y cuando exista un registro de falta en el dia
                        if(!$registros){

                            $newregister = new Asistencia();
                            $newregister->id_aux_treg = 9;// vacaciones
                            $newregister->id_employe = $employe->id;
                            $newregister->synchronized_users = "[]";
                            $newregister->id_function = $employe->id_function;
                            $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                            // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                            $newregister->id_employe_type = $employe->id_employe_type;
                            $newregister->dir_ind = $employe->dir_ind;
                            $newregister->type = $employe->type;
                            $newregister->id_proceso = $employe->id_proceso;
                            $newregister->basico = $employe->remuneracion;
                            $newregister->c_costo = $employe->c_costo;
                            $newregister->created_at = $fecha1->toDateTimeString();
                            $newregister->deleted_at = $fecha2->toDateTimeString();
                            $newregister->created_at_search = $fecha1->toDateString();
                            $newregister->turno = "S/T";
                            $newregister->uniqueReg = $fecha1->toDateString().".9.".$employe->id.".S/T";
                            $newregister->save();
                            //eliminar todos los registros que esten dentro de las vacaciones

                        }else{
                            if($fecha1->gte($registros->deleted_at)){
                                $newregister = new Asistencia();
                                $newregister->id_aux_treg = 9;// vacaciones
                                $newregister->id_employe = $employe->id;
                                $newregister->synchronized_users = "[]";
                                $newregister->id_function = $employe->id_function;
                                $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
                                // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
                                $newregister->id_employe_type = $employe->id_employe_type;
                                $newregister->dir_ind = $employe->dir_ind;
                                $newregister->type = $employe->type;
                                $newregister->id_proceso = $employe->id_proceso;
                                $newregister->basico = $employe->remuneracion;
                                $newregister->c_costo = $employe->c_costo;
                                $newregister->created_at = $fecha1->toDateTimeString();
                                $newregister->deleted_at = $fecha2->toDateTimeString();
                                $newregister->created_at_search = $fecha1->toDateString();
                                $newregister->turno = "S/T";
                                $newregister->uniqueReg = $fecha1->toDateString().".9.".$employe->id.".S/T";

                                $newregister->save();
                            }
                        }
                    }
                }else{
                    $result['title'] = "Incorrecto";
                    $result['icon'] = "warning";
                    $result['message'] = "Fecha de termino es incorrecta.";
                }
            }else{
                $result['title'] = "Incorrecto";
                $result['icon'] = "warning";
                $result['message'] = "Fecha de termino es incorrecta.";
            }

            $caledardata = Asistencia::where('id_employe','=',$employe_id)
            ->select('aux_type_reg.description as title','aux_type_reg.color as backgroundColor',
            DB::raw('concat("javascript:editRegister(",reg_assistance.id,")") as url'),
            'aux_type_reg.color as borderColor',
            'reg_assistance.created_at as start',
            'reg_assistance.deleted_at as end')->join('aux_type_reg', 'reg_assistance.id_aux_treg', '=', 'aux_type_reg.id')->get();

            $result['calendardata'] = json_encode($caledardata);
        } catch (\Throwable $th) {
            //throw $th;
            $result['title'] = "Error";
            $result['icon'] = "error";
            $result['message'] = $th->getMessage();
        }
        return response()->json($result,200,[]);
    }

    public function asistencia(Request $req,$employe,$fecha1,$fecha2,$morning,$evening){
        $resolution = CarbonInterval::minutes();
        $result = array();
        $result['title'] = "Incorrecto";
        $result['icon'] = "warning";
        $result['message'] = "Existe un registro que impide la asistencia.";
        $turno = $fecha1->between($morning, $evening, true) ? "DIA":"NOCHE";
        $id_aux_treg = 1;
        $newregister = new Asistencia();
        $newregister->id_aux_treg = $id_aux_treg;// asistencia
        $newregister->id_employe = $employe->id;
        $newregister->synchronized_users = "[]";
        $newregister->id_function = $employe->id_function;
        $newregister->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];
        // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
        $newregister->id_employe_type = $employe->id_employe_type;
        $newregister->dir_ind = $employe->dir_ind;
        $newregister->type = $employe->type;
        $newregister->id_proceso = $employe->id_proceso;
        $newregister->basico = $employe->remuneracion;
        $newregister->c_costo = $employe->c_costo;
        $newregister->created_at = $fecha1->toDateTimeString();
        $newregister->turno = $turno;
        $newregister->uniqueReg = $fecha1->toDateString().".".$id_aux_treg.".".$employe->id.".".$turno;
        if($req->d_since_at && $req->d_until_at){
            $mins = $fecha1->diffInMinutes($fecha2, true);
            // $horas = $ingreso->diffinHours($salida);
            $horas = $mins/60;
            // check ingreso is greater than salida
            if($fecha2->gt($fecha1)){
                if($horas >= 23){
                    $result['title'] = "ERROR";
                    $result['icon'] = "error";
                    $result['message'] = "Error la diferencia horaria supera las 23 horas.";
                }else{

                    $newregister->hasDinner = $hasDinner = ($req->hasDinner == "1" ? 1 : 0);

                    if($fecha1->dayOfWeek == Carbon::SUNDAY){
                        //añadir validacion de dias asistidos en la semana == 6
                        $pago_real = (($employe->remuneracion / 8)* 2) * $horas;
                        $newregister->paga_100 = round($pago_real *2,2);
                        $newregister->horas_100 = $horas;
                    }else{
                        $horas_nocturna = 0;
                        $horas = $horas > 8 ? $horas - $hasDinner : $horas;

                        $pago_real = ($employe->remuneracion / 8) * ( $horas >= 8 ? 8 : $horas );
                        $horas_extras = $horas > 8 ? $horas - 8 : 0;

                        $horas_25 = $h_25 = $horas_extras > 2 ? 2 : $horas_extras;//acumula
                        $horas_35 = $horas_extras > 2? $horas_extras - $h_25: 0;

                        $paga_25 = $employe->remuneracion/ 8 * 1.25 * $horas_25;
                        $paga_35 = $employe->remuneracion/ 8 * 1.35 * $horas_35;

                        $prima = $horas_35 > 1 ?$employe->remuneracion/ 8 * 1.35 * ($horas_35 - 1):0;

                        // print($inicio->format('Y-m-d'));
                        $inicio_limite = Carbon::create($fecha1->year, $fecha1->month, $fecha1->day, 22, 0, 0); //set time to 22:00 : 10PM
                        $final_limite = Carbon::create($fecha2->year, $fecha2->month, $fecha2->day, 6, 0, 0); //set time to 06:00 : 6AM
                        $minutes = 0;

                        if($inicio_limite->between($fecha1,$fecha2,true) || $final_limite->between($fecha1,$fecha2,true)) {
                            // check if 10PM is between start and end date equal in 6AM
                            try {
                                    $minutes = $fecha1->diffFiltered($resolution, function (Carbon $date) {
                                        //print($date->toDateTimeString());
                                        //$this->exception_text = $date->hour . ":" . $date->minute. "\n";
                                        //$this->exception = $date->toDateTimeString();
                                        return ($date->hour <= 6 && $date->hour >= 0 ) || ($date->hour >= 22 && $date->hour <= 23);
                                    }, $fecha2);
                                    //exit;
                            } catch (\Exception $e) {
                                //print($fecha1->format('Y-m-d H:i:s') . " " . $fecha2->format('Y-m-d H:i:s'));
                                //print($this->exception);
                                dd($e);
                                exit;
                            }
                            $horas_nocturna = $minutes/60;//total de horas en formato decimal
                        }
                        //dd(($employe->remuneracion/6) + (21.7/6));
                        $paga_nocturna = $employe->remuneracion/ 8 * 1.35 * $horas_nocturna;
                        $pago_descanso = $employe->remuneracion/6;
                        $pago_bono_familiar = 21.7/6;
                        $remu = round(
                            ($pago_real +
                                $paga_25 +
                                $paga_35 +
                                $paga_nocturna +
                                $pago_descanso +
                            $pago_bono_familiar) *
                        1.1434 ,2);//factor que paga la empresa

                        $newregister->paga = round($remu,2);

                        $newregister->horas_trabajadas = sprintf('%02d:%02d:00', (int) $horas, fmod($horas, 1) * 60);
                        $newregister->paga_25 = $paga_25;
                        $newregister->horas_25 = sprintf('%02d:%02d:00', (int) $horas_25, fmod($horas_25, 1) * 60);
                        $newregister->paga_35 = $paga_35;
                        $newregister->horas_35 = sprintf('%02d:%02d:00', (int) $horas_35, fmod($horas_35, 1) * 60);
                        $newregister->prima_produccion = $prima;
                        $newregister->horas_prima_produccion =  $horas_35 > 1 ? sprintf('%02d:%02d:00', (int) $horas_35-1, fmod($horas_35-1, 1) * 60):'00:00:00';
                        $newregister->paga_nocturna = $paga_nocturna;
                        $newregister->horas_nocturna = sprintf('%02d:%02d:00', (int) $horas_nocturna, fmod($horas_nocturna, 1) * 60);
                        $newregister->paga_descanso = $pago_descanso;
                        $newregister->paga_bono_familiar = $pago_bono_familiar;
                    }
                    // $javas_pas->paga = round(($employe->remuneracion + ($employe->remuneracion/8 * 1.25 * $horas_25) + ($employe->remuneracion/8 * 1.35 * $horas_35) +  ($employe->remuneracion/6) + (21.7/6)) * 1.1434,2);
                    $newregister->created_at_search = $fecha1->toDateString();
                    $newregister->created_at = $fecha1->toDateTimeString();
                    $newregister->deleted_at = $fecha2->toDateTimeString();

                    // $newregister->synchronized_users = "[]";
                    $newregister->save();

                    $reg_un = RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$fecha1->toDateString())->first();
                    $reg_em = RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$fecha1->toDateString())->first();

                    if($reg_un || $reg_em){
                        $params = ($reg_un ? [$reg_un->id_user,$reg_un->created_at,1] : ($reg_em ? [$reg_em->id_user,$reg_em->created_at,1] : [null,null,0]) );
                        $newregister->id_user_checked = $params[0];
                        $newregister->checked_at = $params[1];
                        $newregister->checked = $params[2];
                        $newregister->synchronized_users = "[]";
                        $newregister->save();
                    }

                    RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$fecha1->toDateString())->forceDelete();
                    RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$fecha1->toDateString())->forceDelete();
                    //delete FALTA

                    $result['title'] = "Correcto";
                    $result['icon'] = "success";
                    $result['message'] = "Asistencia registrada.";
                }
            }else{
                $result['title'] = "ERROR";
                $result['icon'] = "error";
                $result['message'] = "Error fecha inicial mayor a la fecha de cierre.";
            }
        }elseif($req->d_since_at && !$req->d_until_at){
            $newregister->created_at = $fecha1->toDateTimeString();
            $newregister->created_at_search = $fecha1->toDateTimeString();
            $newregister->save();
        }else{
            $result['title'] = "Datos Incorrectos";
            $result['icon'] = "error";
            $result['message'] = "No se puede registrar, sin fecha y hora de ingreso.";
        }

        return $result;
    }
}
