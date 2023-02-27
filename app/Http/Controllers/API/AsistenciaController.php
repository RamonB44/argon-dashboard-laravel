<?php

namespace App\Http\Controllers\API;

use App\Models\Area;
use App\Models\Asistencia;
use App\Models\Employes;
use App\Models\Horario;
use App\Http\Controllers\Controller;
use App\Models\RegEmployes;
use App\Models\RegUnchecked;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Exception;

class AsistenciaController extends Controller
{
    private $_token = "GEQ9IgcuLvF2I0rsFGtnFH115oFinA0La6YIAUjdgu0MVJ3vBSD4fZUjMwsd7DJHy4qVYvBro8qEMKBXYd5j6044eqhEmJtmjHqM";
    //async function
    public function create(Request $req){
        // cooldown 10 minute
        $minutes = 120;
        $hora_ingreso = null;
        $hora_salida = null;

        $employe = Employes::where('code', '=', $req->code)->orWhere('doc_num', '=', $req->code)->first();
        $area_horario = (isset($employe->funcion->areas))?Area::where('id','=',$employe->funcion->areas->id)->first():null;
        $reg_horario = (isset($area_horario->id))?Horario::where('id_area','=',$area_horario->id)->whereDate('created_at', '=', Carbon::today()->toDateString())->first():null;

        if(!isset($area_horario->hora_ingreso)){
            if($reg_horario){
                $hora_ingreso = Carbon::parse($reg_horario->since_at)->format('H');//hora de ingreso;
                $hora_salida = Carbon::parse($reg_horario->until_at)->format('H');//hora de salida;
            }
        }else{
            $hora_ingreso = Carbon::parse($area_horario->hora_ingreso)->format('H');
            $hora_salida = Carbon::parse($area_horario->hora_salida)->format('H');
        }

        $message['success'] = false;
        $message['message'] = "No encontrado";
        if($employe){
            $javas_pas = Asistencia::withTrashed()->where('id_employe', '=', $employe->id)->whereDate('created_at', '=', Carbon::today()->toDateString())->orderBy('created_at','desc')->first();
            if($employe->turno == "NOCHE"){
                //busca dia anterior
                $javas_pas = Asistencia::where('id_employe', '=', $employe->id)->whereDate('created_at', '=', Carbon::today()->subDay()->toDateString())->orderBy('created_at', 'desc')->first();
            }
            $message['success'] = false;

            if(!$javas_pas || $javas_pas->aux_type->aditionable){
                //then a new register
                if(Carbon::now()->format('H') < $hora_ingreso || $hora_ingreso == null){
                    $javas_pas = new Asistencia();
                    $javas_pas->id_employe = $employe->id;
                    $javas_pas->id_aux_treg = 1;
                    $javas_pas->id_function = $employe->id_function;
                    $javas_pas->synchronized_users = "[]";
                    $javas_pas->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0];//register asistance with first sede register from user
                    $javas_pas->save();
                    $message['success'] = true;
                    // $nombres = ;
                    // $name = explode(" ",$employe->fullname);
                    $message['message'] = Carbon::now()->format('g:i A')." Asistencia correcta " . $this->explodeFullname($employe->fullname);
                }else{
                    $message['success'] = false;
                    $message['message'] = "Ha sobrepasado la hora de ingreso";
                }


            }else{
                $message['success'] = false;
                $message['message'] = "Asistencia registrada" . $employe->fullname;
                if($javas_pas->deleted_at){
                    $message['message'] = "Ya registraste una ".$javas_pas->aux_type->description;
                }else{
                    //here cooldown time for beat
                    if ($javas_pas->created_at->diffInMinutes(Carbon::now()) > $minutes) {
                        //complete cooldown time then register a beat
                        if(Carbon::now()->format('H') > $hora_salida || $hora_ingreso == null){
                            $javas_pas->synchronized_users = "[]";
                            $javas_pas->deleted_at = Carbon::now()->toDateTimeString();
                            $javas_pas->save();
                            $message['success'] = true;
                            $message['message'] = Carbon::now()->format('g:i A')." Salida registrada " . $this->explodeFullname($employe->fullname);
                        }else{
                            $message['success'] = false;
                            $message['message'] = "No es tu hora de salida";
                        }
                    }else{
                        $message['message'] = "Espera " . ABS($javas_pas->created_at->diffInMinutes(Carbon::now()) - ($minutes)) . " minutos para registrar su Salida";
                    }
                }
            }

            // Asistencia::updateOrCreate()
        }

        return response()->json(["response" => $message['message']], 200, []);
    }

    private function explodeFullname($full_name){
        /* separar el nombre completo en espacios */
        $tokens = explode(' ', trim($full_name));
        /* arreglo donde se guardan las "palabras" del nombre */
        $names = array();
        /* palabras de apellidos (y nombres) compuetos */
        $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'san', 'santa');

        $prev = "";
        foreach($tokens as $token) {
            $_token = strtolower($token);
            if(in_array($_token, $special_tokens)) {
                $prev .= "$token ";
            } else {
                $names[] = $prev. $token;
                $prev = "";
            }
        }

        $num_nombres = count($names);
        $nombres = $apellidos = "";
        switch ($num_nombres) {
            case 0:
                $nombres = '';
                break;
            case 1:
                $apellidos = $names[0];
                break;
            case 2:
                $nombres    = $names[0];
                $apellidos  = $names[1];
                break;
            case 3:
                $nombres = $names[0];
                $apellidos = $names[1]. ' ' . $names[0];
            case 4:
                $nombres = $names[0]. ' ' . $names[1];
                $apellidos = $names[2]. ' ' . $names[0];
            break;
            default:
                $nombres = $names[1] . ' ' . $names[0];
                unset($names[0]);
                unset($names[1]);
                $apellidos = implode(' ', $names);
            break;
        }
        return $apellidos;
    }

    public function getData(){

        try{
            $config = Auth::user()->getConfig();
            $asistencia = Asistencia::withTrashed()->where('synchronized_users','not like',"%".Auth::user()->id."%")->whereIn('id_sede',$config["sedes"])->get()->toArray();
            // return response()->json($asistencia, 200, []);
            foreach(Asistencia::withTrashed()->where('synchronized_users','not like',"%".Auth::user()->id."%")->whereIn('id_sede',$config["sedes"])->get() as $key => $value){
                //save user backup synchronized
                $array = array();
                if(count(json_decode($value->synchronized_users))>0){
                    //exist elements
                    $array = array_push(json_decode($value->synchronized_users),Auth::user()->id);
                    // $array[] = Auth::user()->id;
                }else{
                    //no exist
                    $array[] = Auth::user()->id;
                }
                $value->synchronized_users = json_encode($array);
                $value->save();
            }
        }catch(\Throwable $th){
            return response()->json(["code" => $th->getMessage()], 200, []);
        }
        // dd($asistencia);
        return response()->json($asistencia, 200, []);
    }

    public function getEmployes(){

        try{
            $config = Auth::user()->getConfig();
            $asistencia = Employes::withTrashed()->where('synchronized_users','not like',"%".Auth::user()->id."%")->get()->toArray();
            // return response()->json($asistencia, 200, []);
            foreach(Employes::withTrashed()->where('synchronized_users','not like',"%".Auth::user()->id."%")->get() as $key => $value){
                //save user backup synchronized
                $array = array();
                if(count(json_decode($value->synchronized_users))>0){
                    //exist elements
                    $array = array_push(json_decode($value->synchronized_users),Auth::user()->id);
                    // $array[] = Auth::user()->id;
                }else{
                    //no exist
                    $array[] = Auth::user()->id;
                }
                $value->synchronized_users = json_encode($array);
                $value->save();
            }
        }catch(\Throwable $th){
            return response()->json(["code" => $th->getMessage()], 200, []);
        }
        // dd($asistencia);
        return response()->json($asistencia, 200, []);
    }

    public function checked(Request $req){
        // Carbon::setLocale('es_PE');
        //this function checked assitance
        $employe = Employes::where('code', '=', $req->code)->orWhere('doc_num', '=', $req->code)->first();
        if($employe){
            $javas_pas = Asistencia::where('id_aux_treg','=',1)->where('id_employe', '=', $employe->id)->whereDate('created_at', '=', Carbon::today()->toDateString())->orderBy('created_at','desc')->first();
            // dd($javas_pas);
            if(isset($javas_pas->created_at)){
                if($javas_pas->deleted_at){
                    $message["message"] = "ya se cuenta con un registro de ".$javas_pas->aux_type->description." concluido";
                    if($javas_pas->checked){
                        $message["message"] .= " verificar";
                    }else{
                        $message["message"] .= " sin verificar";
                    }
                }else{
                    if($javas_pas->checked){
                        $message["message"] = "ya fue verificada";
                    }else{
                        $javas_pas->checked = 1;
                        $javas_pas->synchronized_users = "[]";
                        $javas_pas->checked_at = Carbon::now()->toDateTimeString();
                        $javas_pas->id_user_checked = Auth::user()->id;
                        $javas_pas->save();

                        DB::table('reg_unchecked')->join('employes',function($join){
                        $join->On('reg_unchecked.reg_code','=','employes.code');
                        $join->orOn('reg_unchecked.reg_code','=','employes.doc_num');
                        })->where('employes.code',$employe->code)->whereDate('reg_unchecked.created_at',Carbon::today())->delete();

                        DB::table('reg_employes')->join('employes',function($join){
                            $join->On('reg_employes.code','=','employes.code');
                            $join->orOn('reg_employes.code','=','employes.doc_num');
                        })->where('employes.code',$employe->code)->whereDate('reg_employes.created_at',Carbon::today())->delete();
                        $message["message"] = "asistencia verificada";
                    }
                }
            }else{
                //reg in unchecked table
                 DB::table('reg_unchecked')->whereDate('created_at',Carbon::now()->toDateString())
                    ->updateOrInsert(
                        ['reg_code'=>$req->code],
                        ['id_user' => Auth::user()->id,
                        'id_sede' => json_decode(Auth::user()->user_group->first()->pivot->sedes)[0],
                        'id_proceso' => $employe->id_proceso,
                        'message'=> $employe->fullname,
                        'created_at'=>Carbon::now()->toDateTimeString()]
                );

                $message["message"] = "El trabajador ".$employe->fullname . " no cuenta con asistencia";
            }
        }else{
                DB::table('reg_employes')->whereDate('created_at',Carbon::now()->toDateString())
                    ->updateOrInsert(
                        ['code'=>$req->code],
                        ['id_user' => Auth::user()->id,
                        'sede_id' => json_decode(Auth::user()->user_group->first()->pivot->sedes)[0],//
                        'description'=> "El trabajador no se encuentra registrado",
                        'created_at'=>Carbon::now()->toDateTimeString()]
                );
            $message["message"] = "Empleado no registrado";
        }
        return response()->json(["response" => $message['message']], 200, []);
    }

    public function register(Request $req){
        return reponse()->json(200,json_encode($req->all()),[]);
    }

    public function searchEmploye(Request $req){
        $response = array();
        try{
            $emp_name = Employes::where('code',$req->sap_code)->orWhere('doc_num',$req->sap_code)->first();
            $response["fullname"] = empty($emp_name) ? "TRABAJADOR NO REGISTRADO": $emp_name->fullname;
            $response["sap_code"] = empty($emp_name) ? $req->sap_code: $emp_name->code;
            $response["is_assisted"] = empty($emp_name) ? 0:
            ( Asistencia::where('id_employe',$emp_name->id)
            ->where('id_aux_treg',1)
            ->whereDate('created_at',\Carbon\Carbon::today()->toDateString())
            // ->whereNull('deleted_at')
            ->count() > 0 ? 1 : 0);
        }catch(\Throwable $th){
            return response()->json(["message" => $th->getMessage()], 200, []);
        }
        return response()->json($response,200,[]);
    }

    public function filterEmployes(Request $req){
        $response = array();
        // return response()->json($req->all(),200,[]);
        try{
        $sap_codes = Employes::whereIn('code',$req->all())->select('code as sap_code')->get()->toArray();
        // dd($sap_codes);
        $fullnames = Employes::whereIn('code',$req->all())->select('fullname')->get()->toArray();
        $response["sap_codes"] = (array) $sap_codes;
        $response["fullnames"] = (array) $fullnames;

        //     $emp_name = Employes::where('code',$req->sap_code)->first();
        //     $response["fullname"] = empty($emp_name) ? "TRABAJADOR NO REGISTRADO": $emp_name->fullname;
        }catch(\Throwable $th){
            return response()->json(["message" => $th->getMessage()], 200, []);
        }
        return response()->json($response,200,[]);
    }

    public function setAssistance(String $__token ,String $sap,int $in_out,int $sede, $unixTimeStamp = null){
        if($__token === $this->_token){
            $time = Carbon::now()->timezone('America/Lima');
            if($unixTimeStamp!= null){
                $time = Carbon::createFromTimestamp($unixTimeStamp);
            }

            if($in_out == 1){
                return $this->generated_created_at($sap,$sede,$time);
            }else if($in_out == 4){
                return $this->generated_deleted_at($sap,$sede,$time);
            }
        }
        return response()->json(["success" => false,"message"=> "Harry!!!?, que nadie te vea por estos lares podrian pensar que andas en malos pasos!"],200,[]);
    }

    public function generated_deleted_at($string,$id_sede,$time){
        //se calcula el pago deacuerdo a la horas realizadas , solo jornales;
        //se adiciona pago por horas extras.
        //si el dia es feriado entonces su pago es al 100% incluidas las horas extras.

        $minutes = 60;
        $employe = Employes::where('code', '=', $string)->orWhere('doc_num', '=', $string)->first();
        // dd($employe);

        $message['success'] = false;
        $message['message'] = "No encontrado";
        // dd(Carbon::today()->subDay()->addHour(23)->addMinutes(59)->toDateTimeString());
        // dd($employe->id);
        if($employe){

            $javas_pas = Asistencia::where('id_employe',$employe->id)
            ->whereNotIn('id_aux_treg',[10,3,7])
            ->whereDate('created_at_search','<=',$time->toDateString())
            ->whereNull('deletedAt')
            ->orderBy('created_at_search','desc')
            ->orderBy('created_at','desc')->first();

            if(!$javas_pas){
                $message = $this->salida($employe,$time,$id_sede);
            }else{
                //complete cooldown time then register a beat
                $message["success"] = false;
                if($javas_pas->id_aux_treg == 1){
                    if(!is_null($javas_pas->created_at)){

                        if ($javas_pas->created_at->diffInMinutes(Carbon::now()->timezone('America/Lima')) > $minutes && $javas_pas->id_aux_treg == 1) {
                                //complete cooldown time then register a beat
                                if($javas_pas->created_at->diffInMinutes(Carbon::now()->timezone('America/Lima')) > (23 * 60)){
                                    $message = $this->salida($employe,$time,$id_sede);
                                }else
                                    if($javas_pas->created_at && !$javas_pas->deleted_at){
                                        $resolution = CarbonInterval::minutes();
                                        $fecha1 = Carbon::parse($javas_pas->created_at);
                                        $fecha2 = $time;

                                        $mins = $fecha1->diffInMinutes($fecha2, true);
                                        // $horas = $ingreso->diffinHours($salida);
                                        $horas = $mins/60;//flat

                                        $javas_pas->hasDinner = $hasDinner = $javas_pas->hasDinner;

                                        if($fecha1->dayOfWeek == Carbon::SUNDAY){
                                            $pago_real = (($employe->remuneracion / 8)* 2) * $horas;
                                            $javas_pas->paga_100 = round($pago_real *2,2);
                                            $javas_pas->horas_100 = $horas;
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

                                            $javas_pas->paga = round($remu,2);

                                            $javas_pas->horas_trabajadas = sprintf('%02d:%02d:00', (int) $horas, fmod($horas, 1) * 60);
                                            $javas_pas->paga_25 = $paga_25;
                                            $javas_pas->horas_25 = sprintf('%02d:%02d:00', (int) $horas_25, fmod($horas_25, 1) * 60);
                                            $javas_pas->paga_35 = $paga_35;
                                            $javas_pas->horas_35 = sprintf('%02d:%02d:00', (int) $horas_35, fmod($horas_35, 1) * 60);
                                            $javas_pas->prima_produccion = $prima;
                                            $javas_pas->horas_prima_produccion =  $horas_35 > 1 ? sprintf('%02d:%02d:00', (int) $horas_35-1, fmod($horas_35-1, 1) * 60):'00:00:00';
                                            $javas_pas->paga_nocturna = $paga_nocturna;
                                            $javas_pas->horas_nocturna = sprintf('%02d:%02d:00', (int) $horas_nocturna, fmod($horas_nocturna, 1) * 60);
                                            $javas_pas->paga_descanso = $pago_descanso;
                                            $javas_pas->paga_bono_familiar = $pago_bono_familiar;
                                        }
                                        // $javas_pas->paga = round(($employe->remuneracion + ($employe->remuneracion/8 * 1.25 * $horas_25) + ($employe->remuneracion/8 * 1.35 * $horas_35) +  ($employe->remuneracion/6) + (21.7/6)) * 1.1434,2);
                                        // $javas_pas->paga = round(($employe->remuneracion + ($employe->remuneracion/8 * 1.25 * $horas_25) + ($employe->remuneracion/8 * 1.35 * $horas_35) +  ($employe->remuneracion/6) + (21.7/6)) * 1.1434,2);
                                        $javas_pas->deleted_at = $time->toDateTimeString();
                                        $javas_pas->synchronized_users = "[]";
                                        $javas_pas->save();

                                        $message['success'] = true;
                                        $message['message'] = " Salida registrada " .strtolower($this->explodeFullname($employe->fullname));

                                    }elseif($javas_pas->deleted_at){
                                        //76
                                        $message['message'] = "Ya registraste una " . $javas_pas->aux_type->description . ($javas_pas->id_aux_treg ==1 && $javas_pas->created_at == null ? " solo salida":"");
                                    }
                        } else {
                            $message['message'] = "Espera " . ABS($javas_pas->created_at->diffInMinutes(Carbon::now()) - ($minutes)) . " minutos para registrar su Salida";
                        }
                    }else{
                        $message['message'] = "Tu salida ya fue registrada.";
                    }
                }else{
                    if(Carbon::parse($javas_pas->created_at)->lt($time->toDateString()) && Carbon::parse($javas_pas->deleted_at)->lt($time->toDateString())){
                        // si no encuentra registra el ingreso
                        $message = $this->salida($employe,$time,$id_sede);
                    }else{
                        $message["success"] = false;
                        $message["message"] = "Tienes un registro que impide la asistencia.";
                    }
                }

            }
        }

        $message['html'] = view('javas.response', compact('employe', 'message'))->render();

        return response()->json($message, 200, []);
    }

    public function setDescanso(Carbon $today,Employes $value){
        $days = Asistencia::where('id_aux_treg',1)->whereNull('deletedAt')->where('id_employe',$value->id)->whereBetween('created_at', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()])->count();
        // dd($days);
        if($days == 6){
            //set sunday DESCANSO_SIN_PERMISO by 8 hours
            $asistencia = new Asistencia();
            $asistencia->id_employe = $value->id;
            $asistencia->id_aux_treg = 8;//descanso
            $asistencia->id_employe_type = $value->id_employe_type;
            $asistencia->id_sede = $value->id_sede;
            $asistencia->dir_ind = $value->dir_ind;
            $asistencia->type = $value->type;
            $asistencia->turno = $value->turno;
            $asistencia->id_function = $value->id_function;
            $asistencia->basico = $value->remuneracion;
            $asistencia->paga = $value->remuneracion;
            $asistencia->c_costo = $value->c_costo;
            $asistencia->id_proceso = $value->id_proceso;
            $asistencia->uniqueReg =  $today->copy()->addDay()->toDateString().".8.".$value->id.".S/T";
            $asistencia->created_at = $today->copy()->addDay()->setHours(8)->format('Y-m-d H:i:s');
            $asistencia->deleted_at = $today->copy()->addDay()->setHours(17)->format('Y-m-d H:i:s');
            $asistencia->save();
        }
    }

    public function generated_created_at($string,$id_sede,$time){
        $minutes = 60;
        $message = array();
        $message['success'] = false;
        $message['message'] = "Existe un registro que impide la asistencia.";
        try{
            //$asistencia = Asistencia::where('id_employe', '=', $employe->id)->whereDate('created_at','=',$fecha1->toDateString())->orderBy('created_at', 'desc')->first();
            $morning = Carbon::create($time->year, $time->month, $time->day, 4, 0, 0); //set time to 04:00 : 4AM
            $evening = Carbon::create($time->year, $time->month, $time->day, 14, 0, 0); //set time to 14:00 : 2PM

            $employe = Employes::where('code', '=', $string)->orWhere('doc_num', '=', $string)->first();

            if($employe){

                $register = Asistencia::where('id_employe',$employe->id)
                ->whereNotIn('id_aux_treg',[10,3,7])
                ->whereDate('created_at_search','<=',$time->toDateString())
                ->whereNull('deletedAt')
                ->orderBy('created_at_search','desc')
                ->orderBy('created_at','desc')
                ->first();

                if(!$register){
                    $message = $this->asistencia($id_sede,$employe,$time,null,$morning,$evening);
                }else{
                    if($register->created_at && $register->deleted_at){

                        if(Carbon::parse($register->created_at)->lt($time->toDateString(),false) && Carbon::parse($register->deleted_at)->lt($time->toDateString(),false)){
                            if ($register->created_at->diffInMinutes(Carbon::now()->timezone('America/Lima')) > $minutes) {
                                $message = $this->asistencia($id_sede,$employe,$time,null,$morning,$evening);
                            }
                        }else{
                            if($register->id_aux_treg == 1){
                                $message["success"] = false;
                                $message["message"] = "Ya tienes una asistencia.";
                            }else{
                                $message["success"] = false;
                                $message["message"] = "Tienes un registro que impide la asistencia.";
                            }
                        }

                    }elseif($register->created_at && is_null($register->deleted_at)){
                        //fecha de inicio menor o igual a la fecha de registro
                            //dd($register);
                            if(Carbon::parse($register->created_at)->lt($time->toDateString(),false)){
                                if ($register->created_at->diffInMinutes(Carbon::now()->timezone('America/Lima')) > $minutes) {
                                    $message = $this->asistencia($id_sede,$employe,$time,null,$morning,$evening);
                                }

                            }else{
                                if($register->id_aux_treg == 1){
                                    $message["success"] = false;
                                    $message["message"] = "Ya tienes una asistencia.";
                                }else{
                                    $message["success"] = false;
                                    $message["message"] = "Tienes un registro que impide la asistencia.";
                                }
                            }

                    }elseif(is_null($register->created_at) && $register->deleted_at){
                        //fecha de salida es menor o igual a la fecha de registro
                            if(Carbon::parse($register->deleted_at)->lt($time,false)) {
                                // if ($register->deleted_at->diffInMinutes(Carbon::now()->timezone('America/Lima')) > $minutes) {
                                    $message = $this->asistencia($id_sede,$employe,$time,null,$morning,$evening);
                                // }
                            }else{
                                if($register->id_aux_treg == 1){
                                    $message["success"] = false;
                                    $message["message"] = "Ya tienes una asistencia.";
                                }else{
                                    $message["success"] = false;
                                    $message["message"] = "Tienes un registro que impide la asistencia.";
                                }
                            }
                    }
                }

            }else{
                $message["success"] = false;
                $message["message"] = "Trabajador no cuenta con registro en el sistema.";
            }
        }catch(\Throwable $th){
            // dd($ex);
            if($th->getCode() == 23000) //Violation integrity
            {
                $message['html'] = "ERROR";
                $message['message'] = "Ya cuentas con asistencia.";
                return response()->json($message, 200, []);
            }
            $message['html'] = "ERROR";
            $message['message'] = "Error de servidor interno.";
            return response()->json($message, 500, []);
        }

        $message['html'] = view('javas.response', compact('employe', 'message'))->render();

        return response()->json($message, 200, []);
    }

    private function asistencia($id_sede,$employe,$fecha1,$morning,$evening){
        $turno = $fecha1->between($morning, $evening, true) ? "DIA":"NOCHE";
        $id_aux_treg = 1;
        $newregister = new Asistencia();
        $newregister->id_aux_treg = $id_aux_treg;// asistencia
        $newregister->id_employe = $employe->id;
        $newregister->synchronized_users = "[]";
        $newregister->id_function = $employe->id_function;
        $newregister->id_sede = $id_sede;
        // $newregister->color = AuxiliarTypeReg::find($req->description)->select('color')->first();
        $newregister->id_employe_type = $employe->id_employe_type;
        $newregister->dir_ind = $employe->dir_ind;
        $newregister->type = $employe->type;
        $newregister->id_proceso = $employe->id_proceso;
        $newregister->basico = $employe->remuneracion;
        $newregister->c_costo = $employe->c_costo;
        $newregister->created_at = $fecha1->toDateTimeString();
        $newregister->created_at_search = $fecha1->toDateString();
        $newregister->turno = $turno;
        //columna unica para validar registros duplicados
        $newregister->uniqueReg = $fecha1->toDateString().".".$id_aux_treg.".".$employe->id.".".$turno;

        $reg_un = RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$fecha1->toDateString())->first();
        $reg_em = RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$fecha1->toDateString())->first();

        if($reg_un || $reg_em){
            $params = ($reg_un ? [$reg_un->id_user,$reg_un->created_at,1] : ($reg_em ? [$reg_em->id_user,$reg_em->created_at,1] : [null,null,0]) );
            $newregister->id_user_checked = $params[0];
            $newregister->checked_at = $params[1];
            $newregister->checked = $params[2];
            $newregister->synchronized_users = "[]";
        }

        $newregister->save();
        RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$fecha1->toDateString())->forceDelete();
        RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$fecha1->toDateString())->forceDelete();
        //delete FALTA
        $message['success'] = true;
        $message['message'] = "Asistencia " . strtolower($this->explodeFullname($employe->fullname));

        return $message;
    }

    private function salida($employe,$time,$id_sede){
        // si no encuentra registra el ingreso
        $id_aux_treg = 1;
        $javas_pas = new Asistencia();
        $javas_pas->id_employe = $employe->id;
        $javas_pas->id_aux_treg = $id_aux_treg;
        $javas_pas->id_function = $employe->id_function;
        $javas_pas->id_employe_type = $employe->id_employe_type;
        $javas_pas->dir_ind = $employe->dir_ind;
        $javas_pas->type = $employe->type;
        $javas_pas->turno = "S/T";
        $javas_pas->basico = $employe->remuneracion;
        $javas_pas->paga = $employe->remuneracion;
        $javas_pas->c_costo = $employe->c_costo;
        $javas_pas->synchronized_users = "[]";
        $javas_pas->id_proceso = $employe->id_proceso;
        $javas_pas->deleted_at = $time->toDateTimeString();
        $javas_pas->created_at_search = $time->toDateString();
        $javas_pas->created_at = null;
        $javas_pas->id_sede = $id_sede; //register asistance with first sede register from user
        $javas_pas->uniqueReg = $time->toDateString().".".$id_aux_treg.".".$employe->id.".S/T";
        $javas_pas->save();


        $reg_un = RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$time->toDateString())->first();
        $reg_em = RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$time->toDateString())->first();

        if($reg_un || $reg_em){
            $params = ($reg_un ? [$reg_un->id_user,$reg_un->created_at,1] : ($reg_em ? [$reg_em->id_user,$reg_em->created_at,1] : [null,null,0]) );
            $javas_pas->id_user_checked = $params[0];
            $javas_pas->checked_at = $params[1];
            $javas_pas->checked = $params[2];
            $javas_pas->synchronized_users = "[]";
            $javas_pas->save();
        }

        RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$time->toDateString())->forceDelete();
        RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$time->toDateString())->forceDelete();
        //delete FALTA


        $message['success'] = true;
        $message['message'] = "Salida registrada " . strtolower($this->explodeFullname($employe->fullname));

        return $message;
    }
}
