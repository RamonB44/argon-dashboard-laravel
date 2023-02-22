<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Asistencia;
use App\Models\Auxiliar\TypeReg as AuxiliarTypeReg;
use App\Models\Employes;
use App\Models\Horario;
use App\Models\Auxiliar\Holidays;
use App\QueryFilter\QueryFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
// use App\Models\Asistencia;
use App\Excel\AsistenciaImport;
use App\Models\RegEmployes;
use App\Models\RegUnchecked;
use Carbon\CarbonInterval;

// use function GuzzleHttp\json_decode;

class AsistenciaController extends Controller
{
    //
    private $config;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Request $req,$string){

        if (Auth::user()->hasGroupPermission("registerIngreso")) {
            // return view('asistencia.ingreso');
            return $this->generated_created_at($string);
        }elseif(Auth::user()->hasGroupPermission("registerSalida")){
             return $this->generated_deleted_at($string);
        }

        return response()->json(["message" => "No tienes sufienctes permisos","success"=>false],200,[]);
    }

    public function generated_created_at($string){
        //consultar dias de asistencia total del empleado .
        //si cumple 6 dias de asistencia o 5 y un descanso entonces se le generara un descanso el domingo
        //depende del encargado de asistencias si el personal asistira y se le pagara doble o solo su descanso dominical.
        //verificar el siguiente dia si es feriado o descanso dominical.
        //EJECUTAR JOB TODOS LOS SABADOS A LAS 22:00 HORAS PARA ASIGNAR DESCANSO DOMINICAL DE FORMA AUTOMATICA

        $time = Carbon::now()->timezone('America/Lima');
        $morning = Carbon::create($time->year, $time->month, $time->day, 4, 0, 0); //set time to 04:00 : 4AM
        $evening = Carbon::create($time->year, $time->month, $time->day, 14, 0, 0); //set time to 14:00 : 2PM

        $employe = Employes::where('code', '=', $string)->orWhere('doc_num', '=', $string)->first();

        $message['success'] = false;
        $message['message'] = "No encontrado";
        if($employe){
            $javas_pas = Asistencia::where('id_employe','=', $employe->id)
            ->whereDate('created_at_search',Carbon::today()->toDateString())
            ->whereNull('deletedAt')
            ->orderBy('created_at_search','desc')
            ->orderBy('created_at','desc')
            ->first();//registro

            if(!$javas_pas){
                // si no encuentra registra el ingreso
                //$javas_pas->id_aux_treg = 10 is falta
                $javas_pas = new Asistencia();
                $javas_pas->id_employe = $employe->id;
                $javas_pas->id_aux_treg = 1;
                $javas_pas->id_function = $employe->id_function;
                $javas_pas->id_employe_type = $employe->id_employe_type;
                $javas_pas->dir_ind = $employe->dir_ind;
                $javas_pas->type = $employe->type;
                $javas_pas->basico = $employe->remuneracion;
                $javas_pas->c_costo = $employe->c_costo;
                $javas_pas->id_proceso = $employe->id_proceso;

                if($time->between($morning, $evening, true)) {
                    //current time is between morning and evening
                    $javas_pas->turno = "DIA";
                } else {
                    $javas_pas->turno = "NOCHE";
                    //current time is earlier than morning or later than evening
                }
                // $javas_pas->turno = ( Carbon::now()->format('H:i')  );
                $javas_pas->synchronized_users = "[]";
                $javas_pas->created_at = Carbon::now()->timezone('America/Lima')->toDateTimeString();
                $javas_pas->created_at_search = Carbon::now()->timezone('America/Lima')->toDateTimeString();
                $javas_pas->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0]; //register asistance with first sede register from user
                $remu = ($employe->remuneracion +  ($employe->remuneracion/6) + (21.7/6)) * 1.1434;
                if($time->dayOfWeek == Carbon::SUNDAY){
                    $javas_pas->paga = round($remu *2,2);
                }else{
                    $javas_pas->paga = round($remu,2);

                }
                $javas_pas->save();

                //set descanso
                // $today = Carbon::today();
                if($time->dayOfWeek == Carbon::SATURDAY){
                    $this->setDescanso($time,$employe);
                }
                //delete Registro

                //delete FALTA
                Asistencia::where('id_employe',$employe->id)->where('id_aux_treg',10)->whereDate('created_at',Carbon::now()->toDateString())->forceDelete();

                // if(Carbon::today()->isSameDay($time->toDateString())){
                $reg_un = DB::table('reg_unchecked')->join('employes',function($join){
                        $join->On('reg_unchecked.reg_code','=','employes.code');
                        $join->orOn('reg_unchecked.reg_code','=','employes.doc_num');
                    })->where('employes.code',$employe->code)->whereDate('reg_unchecked.created_at',$time->toDateString())->first();
                $reg_em = DB::table('reg_employes')->join('employes',function($join){
                        $join->On('reg_employes.code','=','employes.code');
                        $join->orOn('reg_employes.code','=','employes.doc_num');
                    })->where('employes.code',$employe->code)->whereDate('reg_employes.created_at',$time->toDateString())->first();
                if($reg_un || $reg_em){
                    $params = ($reg_un ? [$reg_un->id_user,$reg_un->created_at,1] : ($reg_em ? [$reg_em->id_user,$reg_em->created_at,1] : [null,null,0]) );
                    $javas_pas->id_user_checked = $params[0];
                    $javas_pas->checked_at = $params[1];
                    $javas_pas->checked = $params[2];
                    $javas_pas->synchronized_users = "[]";
                    $javas_pas->save();
                }

                DB::table('reg_unchecked')->join('employes',function($join){
                    $join->On('reg_unchecked.reg_code','=','employes.code');
                    $join->orOn('reg_unchecked.reg_code','=','employes.doc_num');
                })->where('employes.code',$employe->code)->whereDate('reg_unchecked.created_at',$time->toDateString())->delete();
                DB::table('reg_employes')->join('employes',function($join){
                    $join->On('reg_employes.code','=','employes.code');
                    $join->orOn('reg_employes.code','=','employes.doc_num');
                })->where('employes.code',$employe->code)->whereDate('reg_employes.created_at',$time->toDateString())->delete();
                $message['success'] = true;
                $message['message'] = "Asistencia " . strtolower($this->explodeFullname($employe->fullname));
            }else{
                // DB::table('reg_unchecked')->where('reg_code',$string)->whereDate('created_at',Carbon::now()->toDateString())->delete();
                $message['message'] = "Ya registraste una " . $javas_pas->aux_type->description . ($javas_pas->id_aux_treg ==1 && $javas_pas->created_at == null ? " solo salida":"");
            }
        }else{
            // Asistencia::whereDate('created_at','=',Carbon::today()->toDateString())
            // ->updateOrCreate(["code"=>$string]
            // ,
            // ['id_employe'=> null,
            // 'id_aux_treg'=> 1,
            // 'id_function' => 31,//NUEVOS,
            // 'id_sede'=>json_decode(Auth::user()->user_group->first()->pivot->sedes)[0]
            // ]);
        }

        $message['html'] = view('javas.response', compact('employe', 'message'))->render();

        return response()->json($message, 200, []);
    }

    public function generated_deleted_at($string){
        //se calcula el pago deacuerdo a la horas realizadas , solo jornales;
        //se adiciona pago por horas extras.
        //si el dia es feriado entonces su pago es al 100% incluidas las horas extras.
        $resolution = CarbonInterval::minutes();
        $minutes = 60;
        $employe = Employes::where('code', '=', $string)->orWhere('doc_num', '=', $string)->first();
        // dd($employe);

        $message['success'] = false;
        $message['message'] = "No encontrado";
        // dd(Carbon::today()->subDay()->addHour(23)->addMinutes(59)->toDateTimeString());
        // dd($employe->id);
        if($employe){
            $javas_pas = Asistencia::where('id_employe', $employe->id)
            ->whereNotIn('id_aux_treg',[10,3,7])
            ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:%s")'),'<',Carbon::now()->toDateTimeString())
            ->where(DB::raw('TIMESTAMPDIFF(HOUR, created_at, "'.Carbon::now()->toDateTimeString().'")'),'<',23)
            ->where(DB::raw('TIMESTAMPDIFF(HOUR, created_at, "'.Carbon::now()->toDateTimeString().'")'),'>',1)
            ->whereBetween(DB::raw('DATE_FORMAT(created_at_search, "%Y-%m-%d %H:%i:%s")'),
            [Carbon::now()->setHours(0)->setMinutes(0)->setSeconds(0)->subDay()->toDateTimeString(),//ayer a las 2020-09-08 00:00:00
            Carbon::now()->setHours(23)->setMinutes(59)->setSeconds(59)->toDateTimeString()])//hoy a las 2020-09-08 23:59:59
            //->whereDate('created_at_search',Carbon::parse($row['fecha'])->toDateString())
            ->whereNull('deletedAt')
            ->orderBy('created_at','desc')->first();
            // search today and subday the last register;
            // ->first();
            // dd($javas_pas);
            if(!$javas_pas){
                // si no encuentra registra el ingreso
                $javas_pas = new Asistencia();
                $javas_pas->id_employe = $employe->id;
                $javas_pas->id_aux_treg = 1;
                $javas_pas->id_function = $employe->id_function;
                $javas_pas->id_employe_type = $employe->id_employe_type;
                $javas_pas->dir_ind = $employe->dir_ind;
                $javas_pas->type = $employe->type;
                $javas_pas->turno = "S/T";
                $javas_pas->basico = $employe->remuneracion;
                // $javas_pas->paga = $employe->remuneracion;
                $javas_pas->c_costo = $employe->c_costo;
                $javas_pas->synchronized_users = "[]";
                $javas_pas->id_proceso = $employe->id_proceso;
                $javas_pas->deleted_at = Carbon::now()->toDateTimeString();
                $javas_pas->created_at_search = Carbon::now()->toDateTimeString();
                $javas_pas->created_at = null;
                $javas_pas->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0]; //register asistance with first sede register from user
                $javas_pas->save();
                $message['success'] = true;
                $message['message'] = "Salida registrada " . strtolower($this->explodeFullname($employe->fullname));
            }else{
                //complete cooldown time then register a beat
                $message["success"] = false;
                if ($javas_pas->created_at->diffInMinutes(Carbon::now()) > $minutes) {
                    //complete cooldown time then register a beat
                    if($javas_pas->created_at && !$javas_pas->deleted_at){
                        //registra salida en el registro
                        $fecha1 = new Carbon($javas_pas->created_at);
                        $fecha2 = new Carbon($javas_pas->deleted_at);
                        $mins = $fecha1->diffInMinutes($fecha2, true);
                        // $horas = $ingreso->diffinHours($salida);
                        $horas = $mins/60;

                        if($fecha2->gt($fecha1)){
                            if($horas >= 23){
                                // $result['title'] = "ERROR";
                                $message['success'] = false;
                                $message['message'] = "Error la diferencia horaria supera las 23 horas.";
                            }else{

                                $javas_pas->hasDinner = $hasDinner = ($horas >= 8 ? 1 : 0);

                                if($fecha1->dayOfWeek == Carbon::SUNDAY){
                                    //añadir validacion de dias asistidos en la semana == 6
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
                                // $javas_pas->created_at_search = $fecha1->toDateTimeString();
                                // $javas_pas->created_at = $fecha1->toDateTimeString();
                                $javas_pas->deleted_at = $fecha2->toDateTimeString();

                                // $javas_pas->synchronized_users = "[]";
                                $javas_pas->save();

                                $reg_un = RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$fecha1->toDateString())->first();
                                $reg_em = RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$fecha1->toDateString())->first();

                                if($reg_un || $reg_em){
                                    $params = ($reg_un ? [$reg_un->id_user,$reg_un->created_at,1] : ($reg_em ? [$reg_em->id_user,$reg_em->created_at,1] : [null,null,0]) );
                                    $parametros["id_user_checked"] = $params[0];
                                    $parametros["checked_at"] = $params[1];
                                    $parametros["checked"] = $params[2];
                                    $parametros["synchronized_users"] = "[]";
                                    // $javas_pas->save();
                                }

                                RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$fecha1->toDateString())->forceDelete();
                                RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$fecha1->toDateString())->forceDelete();
                                //delete FALTA
                                // $result['title'] = "Correcto";
                                $message['success'] = true;
                                $message['message'] = " Salida registrada " .strtolower($this->explodeFullname($employe->fullname));
                            }
                        }else{
                            // $result['title'] = "ERROR";
                            // $result['icon'] = "error";
                            $message['success'] = false;
                            $message['message'] = "Error fecha inicial mayor a la fecha de cierre.";
                        }
                    }elseif($javas_pas->deleted_at){
                        //76
                        $message['message'] = "Ya registraste una " . $javas_pas->aux_type->description . ($javas_pas->id_aux_treg ==1 && $javas_pas->created_at == null ? " solo salida":"");
                    }
                } else {
                    $message['message'] = "Espera " . ABS($javas_pas->created_at->diffInMinutes(Carbon::now()) - ($minutes)) . " minutos para registrar su Salida";
                }

            }
        }

        $message['html'] = view('javas.response', compact('employe', 'message'))->render();

        return response()->json($message, 200, []);
    }

    public function setDescanso(Carbon $today,Employes $value){
        $days = Asistencia::where('id_aux_treg',1)->whereNull('deletedAt')->where('id_employe',$value->id)->whereBetween('created_at', [$today->startOfWeek(), $today->endOfWeek()])->count();
        // dd($days);
        if($days >= 6){
            //elimina descanso y añade uno nuevo
            Asistencia::where('id_employe',$value->id)->where('id_aux_treg',8)->whereDate('created_at',$today->toDateString())->forceDelete();
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
            $asistencia->paga = $value->remuneracion;
            $asistencia->c_costo = $value->c_costo;
            $asistencia->created_at = $today->copy()->addDay()->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s');
            $asistencia->deleted_at = $today->copy()->addDay()->setHours(23)->setMinutes(59)->setSeconds(59)->format('Y-m-d H:i:s');
            $asistencia->save();
        }
    }

    public function reportes(){
        if(!Auth::user()->hasGroupPermission("viewRAssistance")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        return view('assistance.assistance');
    }

    public function search(Request $req)
    {
        $sede = $req->sede;
        $checked = $req->checked;
        $code = $req->codigo;
        $end_date = $req->end_date;
        $start_date = $req->start_date;
        $area = $req->area;
        $turno = $req->turno;
        $salida = (int) $req->salida;

        // dd($salida);
        // Carbon::setLocale('es_PE');
        Carbon::setLocale('es');
        $this->config = Auth::user()->getConfig();

        if ($sede == 0) {
            $sede = $this->config['sedes'];
        } else {
            $sede = [$sede];
        }

        if ($area == 0) {
            $area = $this->config['areas'];
        } else {
            $area = [$area];
        }

        if($req->proceso==="ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$this->config['sedes'])->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();

        }else{
            $proceso = [$req->proceso];
        }
        // dd($area);
        //range max 7 days no filter by code
        $items = array();

        $fecha1 = Carbon::parse($start_date);
        $fecha2 = Carbon::parse($end_date);
        // return $fecha1->addDays(1) . "|" . $fecha2;
        $diff = $fecha1->diff($fecha2);

        $paint_row = "";
        //row we would be paint in excel
        $data = [];

        $max_count = $diff->days +  1;

        $painting = $this->getLetterArraybyDays($max_count);

        $headings = array();

        $employes_id = Asistencia::whereIn('id_sede',$sede)
        ->whereIn('id_proceso',$proceso)
        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'),
        [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])->groupBy('id_employe')
        ->select('id_employe')->pluck('id_employe')->toArray();
        // dd($turno)
        if($turno == "S/T"){
            $employes_id = Asistencia::whereIn('id_sede',$sede)
            ->whereIn('id_proceso',$proceso)
            ->whereNull('created_at')
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(deleted_at, "%Y-%m-%d %H:%i"))'),
            [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])->groupBy('id_employe')
            ->select('id_employe')->pluck('id_employe')->toArray();
            //  dd($employes_id);
        }

        $employes = Employes::whereIn('id',$employes_id)->get();

        if ($code != 0) {
            $employes = Employes::where('code', '=', $code)->where('id_proceso',$proceso)->orWhere('doc_num', '=', $code)->get();
        }

        foreach ($employes as $key => $value) {
            # code...
            $items[$key]["Employe_ID"] = $value->id;
            $items[$key]["Turno"] = $value->turno;
            $items[$key]["Sede"] = $value->sedes->name;
            $items[$key]["Tipo"] = $value->type;
            $items[$key]["Area"] = $value->funcion->areas->area;
            $items[$key]["Funcion"] = $value->funcion->description;
            $items[$key]["Codigo"] = $value->code;
            $items[$key]["Documento"] = $value->doc_num;
            $items[$key]["Nombres"] = $value->fullname;
            $items[$key]["CCosto"] = (empty($value->c_costo) ? "No definido":$value->c_costo);
        }

        $treg = AuxiliarTypeReg::withTrashed()->orderBy('id', 'asc')->get();
        $qry = "";
        $qry2 = "";
        $label = array();
        foreach ($treg as $key => $value) {
            # code...
            // $label["label"][0][$key] = $value->description;
            $qry2 .= "SUM(QUERY1.$value->description) as $value->description,";
            $qry .= "case reg_assistance.id_aux_treg when " . $value->id . " then count(*) else 0 end as " . $value->description . ",";
        }
        $qry .= '"ok"';
        $qry2 .= '"ok"';
        foreach ($items as $k => $v) {
            # code...
            $contador_array_delete = 0;
            for ($i = 0; $i < $diff->days + 1; $i++) {
                # code...
                $fecha1 = Carbon::parse($start_date);
                $current = $fecha1->addDays($i);
                $sub = DB::table('reg_assistance')
                    ->select(
                        'reg_assistance.id_employe',
                        'reg_assistance.id_sede',
                        DB::raw($qry)
                    )->join('funct_area','reg_assistance.id_function','funct_area.id')
                    ->when(function ($query) use ($current,$items,$k,$sede,$area,$proceso){
                        // dd("Ok");
                        return  $query->where('reg_assistance.id_employe', '=', $items[$k]["Employe_ID"])
                        ->where('reg_assistance.id_aux_treg',10)
                        ->whereIn('reg_assistance.id_sede', $sede)
                        ->whereIn('funct_area.id_area',$area)
                        ->whereDate('reg_assistance.created_at', '=', $current->format('Y-m-d'))
                        ->whereNull('reg_assistance.deletedAt');
                    }, function ($query) use ($items,$k,$sede,$checked,$area,$turno,$current,$proceso){
                        // dd("nop");
                        return $query->where('reg_assistance.id_employe', '=', $items[$k]["Employe_ID"])
                        ->whereNotIn('reg_assistance.id_aux_treg',[10,6])
                        // ->where('id_proceso',$proceso)
                        ->whereIn('reg_assistance.id_proceso',$proceso)
                        ->whereIn('reg_assistance.id_sede', $sede)
                        ->whereIn('reg_assistance.checked',($checked)?[0]:[0,1])
                        ->whereIn('funct_area.id_area',$area)
                        ->whereIn('reg_assistance.turno',( $turno ? [$turno]:["NOCHE","DIA"] ))
                        ->whereDate('reg_assistance.created_at', '=', $current->format('Y-m-d'))
                        ->whereNull('reg_assistance.deletedAt');
                    })
                    ->groupBy('reg_assistance.id_employe', 'reg_assistance.id_aux_treg', 'reg_assistance.id_sede');
                    // dd($sub->toSql());
                $orden = DB::table(DB::raw("({$sub->toSql()}) as QUERY1"))->select(
                    DB::raw($qry2),
                    'QUERY1.id_employe',
                    'QUERY1.id_sede'
                )->groupBy('QUERY1.id_employe', 'QUERY1.id_sede')
                    ->mergeBindings($sub)
                    ->first();

                // dd($orden->id_sede);
                if($turno == "S/T"){
                    $sub = DB::table('reg_assistance')
                        ->select(
                            'reg_assistance.id_employe',
                            'reg_assistance.id_sede',
                            DB::raw($qry)
                        )->join('funct_area','reg_assistance.id_function','funct_area.id')
                        ->when($salida,function ($query) use ($current,$items,$k,$sede,$area){
                            return $query->where('reg_assistance.id_aux_treg',10)
                            ->where('reg_assistance.id_employe', '=', $items[$k]["Employe_ID"])
                            // ->whereIn('reg_assistance.id_proceso',$proceso)
                            ->whereIn('reg_assistance.id_sede', $sede)
                            ->whereIn('funct_area.id_area',$area)
                            ->whereDate('reg_assistance.created_at', '=', $current->format('Y-m-d'))
                            ->whereNull('reg_assistance.deletedAt');
                        }, function ($query) use ($current,$items,$k,$sede,$area,$checked,$proceso){
                            return $query->where('reg_assistance.id_employe', '=', $items[$k]["Employe_ID"])
                            // ->where('reg_assistance.id_aux_treg',"!=",10)
                            ->whereIn('reg_assistance.id_proceso',$proceso)
                            ->whereIn('reg_assistance.id_sede', $sede)
                            // ->whereIn('reg_assistance.checked',($checked)?[0]:[0,1])
                            ->whereIn('funct_area.id_area',$area)
                            ->where('reg_assistance.turno',"S/T")
                            ->whereNull('reg_assistance.deletedAt')
                            ->whereDate('reg_assistance.deleted_at', '=', $current->format('Y-m-d'));
                        })
                        ->groupBy('reg_assistance.id_employe', 'reg_assistance.id_aux_treg', 'reg_assistance.id_sede');
                    $orden = DB::table(DB::raw("({$sub->toSql()}) as QUERY1"))->select(
                        DB::raw($qry2),
                        'QUERY1.id_employe',
                        'QUERY1.id_sede'
                    )->groupBy('QUERY1.id_employe', 'QUERY1.id_sede')
                        ->mergeBindings($sub)
                        ->first();
                }

                if ($orden) {
                    $contador_array_delete++;
                    // $i[$k] = $orden;
                    // $items[$k]['sede'] = (isset($items[$k]['sede'])?$items[$k]['sede']." | ". $orden->id_sede:  $orden->id_sede);
                    // $items[$k][$current->format('Y-m-d')]  = (isset($orden) ? ((($orden->ASISTENCIA > 0) ? " A " : "") . (($orden->PERMISO > 0) ? " P " : "") . (($orden->LICENCIA_C > 0) ? " LC " : "") . (($orden->LICENCIA_S > 0) ? " LS " : "") . (($orden->LIBRE > 0) ? " L " : "") . (($orden->CESE > 0) ? " C " : "")) : "SR");
                    // $items[$k]['hora'] = (isset($items[$k]['hora'])?$items[$k]['hora']." | ". Carbon::parse($orden->created_at)->format('H:i'): Carbon::parse($orden->created_at)->format('H:i'));

                    $items[$k]['neto'] =  (isset($items[$k]['neto']) ? $items[$k]['neto'] : 0) + ((isset($orden->ASISTENCIA)) ? $orden->ASISTENCIA : 0);
                    // $items[$k]['netoP'] =  (isset($items[$k]['netoP']) ? $items[$k]['netoP'] : 0) + ((isset($orden->PERMISO)) ? $orden->PERMISO : 0);
                    // $items[$k]['netoL'] =  (isset($items[$k]['netoL']) ? $items[$k]['netoL'] : 0) + ((isset($orden->LIBRE)) ? $orden->LIBRE : 0);
                    // $items[$k]['netoLC'] = (isset($items[$k]['netoLC']) ? $items[$k]['netoLC'] : 0) + ((isset($orden->LICENCIA_C)) ? $orden->LICENCIA_C : 0);
                    // $items[$k]['netoLS'] = (isset($items[$k]['netoLS']) ? $items[$k]['netoLS'] : 0) + ((isset($orden->LICENCIA_S)) ? $orden->LICENCIA_S : 0);
                    // $items[$k]['netoC'] = (isset($items[$k]['netoC']) ? $items[$k]['netoC'] : 0) + ((isset($orden->CESE)) ? $orden->CESE : 0);
                }
            }

            if ($contador_array_delete == 0) {
                unset($items[$k]);
            }
        }
        // dd($items);
        // return $qry;
        // return $items;
        $headings[0] = "ID";
        $headings[1] = "Turno";
        $headings[2] = "Sede";
        $headings[3] = "Tipo";
        $headings[4] = "Codigo";
        $headings[5] = "Area";
        $headings[6] = "Funcion";
        $headings[7] = "Documento";
        $headings[8] = "Nombres";
        $headings[9] = "CCosto";

        for ($i = 0; $i < $diff->days + 1; $i++) {
            # code...
            $fecha1 = Carbon::parse($start_date);
            $current = $fecha1->addDays($i);
            if ($current->format('Y-m-d') == $fecha1->format('Y-m-d')) {
                $paint_row = $painting[$i];
            }
            $headings[count($headings)] = $current->translatedFormat('l/d');
        }
        $headings[] = "T.A";
        // $headings[] = "Sede";
        // dd($start_date);
        return view('assistance.response', compact('items', 'headings', 'start_date', 'end_date','turno','salida'))->render();
    }

    //funcion para optimizar
    public function search_new(Request $req){
        Carbon::setLocale('es');
        //datos
        $sede = (int) $req->sede;
        $checked = (int) $req->checked;
        $code = (String) $req->codigo;
        $end_date = (String) $req->end_date;
        $start_date = (String) $req->start_date;
        $area = (int) $req->area;
        $turno = (String )$req->turno;
        $salida = (int) $req->salida;

        $this->config = Auth::user()->getConfig();

        if ($sede == 0) {
            $sede = $this->config['sedes'];
        } else {
            $sede = [$sede];
        }

        if ($area == 0) {
            $area = $this->config['areas'];
        } else {
            $area = [$area];
        }

        if($req->proceso==="ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$this->config['sedes'])->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();
        }else{
            $proceso = [$req->proceso];
        }

        $fecha1 = Carbon::parse($start_date);
        $fecha2 = Carbon::parse($end_date);

        $diff = $fecha1->diffInDays($fecha2);
        $headings = ["Codigo","Trabajador","DIR/IND","Documento","Nombres"];
        // $headings[0] = "Codigo";
        // $headings[1] = "DIR/IND";
        // $headings[2] = "Documento";
        // $headings[3] = "Nombres";

        $sub_query = "";
        for($i = 0; $i < $diff + 1;$i++){
            //creo las cabeceras para totales y obtener los datos
            $f = Carbon::parse($start_date);
            $current = $f->addDays($i);
            //centro,area
            $headings[$current->translatedFormat('l/d')][] = 'CCosto';
            $headings[$current->translatedFormat('l/d')][] = 'Area';
            $headings[$current->translatedFormat('l/d')][] = 'Sede';
            $headings[$current->translatedFormat('l/d')][] = 'Ingreso';
            $headings[$current->translatedFormat('l/d')][] = 'Salida';
            $headings[$current->translatedFormat('l/d')][] = 'OBS';

            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then reg_assistance.c_costo else '' end) as '".$current->format('Y_m_d')."_CCOSTO',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then areas.area else '' end) as '".$current->format('Y_m_d')."_AREA',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then sedes.name else '' end) as '".$current->format('Y_m_d')."_SEDE',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then DATE_FORMAT(reg_assistance.created_at, '%Y-%m-%d %H:%i:%s') else '' end) as '".$current->format('Y_m_d')."_INGRESO',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then DATE_FORMAT(reg_assistance.deleted_at, '%Y-%m-%d %H:%i:%s') else '' end) as '".$current->format('Y_m_d')."_SALIDA',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 and reg_assistance.hasObs then concat('javascript:void(',reg_assistance.id,')') else '' end) as '".$current->format('Y_m_d')."_OBS'".($diff > $i ? ",":"");

            /*$sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".
            $current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then (case when reg_assistance.created_at is null or reg_assistance.deleted_at is null  then concat('javascript:void(',reg_assistance.id,')') else '' end ) else '' end) as '".
            $current->format('Y_m_d')."_VALIDACION'".($diff > $i ? ",":"");*/
            // array_push($headings,$customheadings);
        }

        $headings[count($headings)] = "T.A";
        $headings[count($headings)] = "T.P";
        $headings[count($headings)] = "T.F";

        $sub = DB::table('reg_assistance')
        ->join('sedes','reg_assistance.id_sede','sedes.id')
        ->join('employes','reg_assistance.id_employe','employes.id')
        ->join('employes_type','employes.id_employe_type','employes_type.id')
        ->join('funct_area','employes.id_function','funct_area.id')
        ->join('areas','funct_area.id_area','areas.id')
        ->whereIn('reg_assistance.id_sede',$sede)
        ->whereIn('reg_assistance.id_proceso',$proceso)
        ->whereBetween(DB::raw('DATE_FORMAT(reg_assistance.created_at_search, "%Y-%m-%d")'),
        [$fecha1->format('Y-m-d'), $fecha2->format('Y-m-d')])
        ->select('employes.id as Employe_ID','employes_type.description as Trabajador','employes.dir_ind as DIR_IND','employes.doc_num as Documento','employes.fullname as Nombres',
        DB::raw($sub_query),
        DB::raw('SUM(case when reg_assistance.id_aux_treg = 1 then 1 else 0 end) as T_ASISTENCIA'),
        DB::raw('SUM(case when reg_assistance.id_aux_treg = 3 then 1 else 0 end) as T_PERMISO'),
        DB::raw('SUM(case when reg_assistance.id_aux_treg = 10 then 1 else 0 end) as T_FALTA'),
        )->groupBy('reg_assistance.id_employe')->get()
        ->toArray();
        // dd($headings);
        return view('reports.fragments.table-body',compact('sub','headings'))->render();
    }

    private function getLetterArraybyDays($max_count)
    {
        $contado = 0;
        $concatenar = false;
        $letter = null;
        $contado2 = 0;
        $painting = null;
        $contador3 = 0;

        for ($i = 65; $i <= 90; $i++) {
            $letter = chr($i);
            if ($concatenar) {
                $letter = $painting[$contado2] . chr($i);
            }
            $painting[$contado] = $letter;
            $contado++;
            $max_count--;
            if ($contado == 26 || $max_count <= 0) {
                $contador3++;
                $concatenar = true;
                if ($max_count <= 0) {
                    break;
                } else {
                    $i = 64;
                    if ($contador3 > 1) {
                        $contado2++;
                    }
                }
            }
        }
        return $painting;
    }

    private function explodeFullname($full_name)
    {
        /* separar el nombre completo en espacios */
        $tokens = array_reverse(explode(' ', trim($full_name)));
        /* arreglo donde se guardan las "palabras" del nombre */
        $names = array();
        /* palabras de apellidos (y nombres) compuetos */
        $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'san', 'santa');

        $prev = "";
        foreach ($tokens as $token) {
            $_token = strtolower($token);
            if (in_array($_token, $special_tokens)) {
                $prev .= "$token ";
            } else {
                $names[] = $prev . $token;
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
                $apellidos = $names[1] . ' ' . $names[0];
            case 4:
                $nombres = $names[0];
                $apellidos = $names[2] . ' ' . $names[1];
                break;
            default:
                $nombres = $names[0];
                unset($names[0]);
                unset($names[1]);
                $apellidos = implode(' ', $names);
                break;
        }
        return $nombres;
    }

    public function collectAsistenciaLocal($date = null)
    {
        //send all not synchronized_users any datet
        $asistencia = Asistencia::withTrashed()->where('synchronized_users','=',0)->get()->toArray();
        // dd($asistencia);
        $conexion = QueryFilter::getInstance();
        $result = $conexion->setQueriesInServer($asistencia);

        // if(json_decode($result)){
            if(json_decode($result)==200 ){
                //forceDelete
                // dd($result);
                Asistencia::withTrashed()->where('synchronized_users','=',0)->forceDelete();
                $result = $this->collectAsistenciaServer($conexion);
            }else{
                $result = "ocurrio un problema codigo de error:".$result;
            }
        // }else{
        //     $result = "nada que sincronizar";
        // }

        //on success 200 ok code , delete all send and wait

        //on success , collect data and insert in local
        //on response delete all force delete all and insert asistance from response;
        return response()->json($result, 200, []);
    }

    public function collectAsistenciaServer(QueryFilter $conexion)
    {
        //this will never be used
        // $conexion = QueryFilter::getInstance();
        $result = $conexion->getQueriesfromServer();
        $datos = json_decode($result);
        $response = array();
        // dd($datos);
        foreach ($datos as $key => $value) {
            # code...
            $primary = $value->id."-S";
            $response[$key] = Asistencia::withTrashed()->updateOrCreate(
                ["sync_id"=>$primary],
                [
                "sync_id" => $primary,
                "code" => $value->code ,
                "temperature"=>$value->temperature ,
                "id_employe" => $value->id_employe ,
                "id_function" => $value->id_function ,
                "id_sede" => $value->id_sede ,
                "id_aux_treg" => $value->id_aux_treg,
                "synchronized_users" => true,
                "checked"=> $value->checked,
                "created_at"=>$value->created_at,
                "deleted_at"=>$value->deleted_at,
                "deletedAt"=>$value->deletedAt
                ]
                );
        }
        return response()->json($response, 200, []);
    }

    public function import_marcas(Request $req){
        try {
            //code...
            if ($req->all()) {

                if ($req->hasfile('file_xlsx')) {
                    $val = $req->in_out == 0 ? false : true;

                    $import = new AsistenciaImport($val);
                    // dd($val);
                    $import->import($req->file('file_xlsx'));

                    // if(!Auth::user()->hasGroupPermission("createEmployes") && !Auth::user()->hasGroupPermission("updateEmployes")){
                    //     //return abort('pages.403');
                    //     return response()->view('pages.403', [], 403);
                    //     // $data["success"] = false;
                    //     // $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    //     // $data["icon"] = "warning";
                    //     // $data["title"] = "Authorization Denied";
                    //     // return response()->json($data, 200, []);
                    // }
                    // $all_inserted = $import->getImportedSuccessfully();//return data imported successfully
                    if(count($import->failures())==0){
                        // Session::flash('success', "Todos los registros fueron importados correctamente");
                        return redirect()->back()->with('success',"Todos los registros fueron importados correctamente");
                    }else{
                        $failures = $import->failures();
                        $errors = $import->errors();
                        return view('excel.failures',compact('failures','errors'));
                        // return ;
                    }
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }

    // parameter @Array $fullData
    public function setMasiveFaltas($fullData){
        // dd($fullData);
        $config = Auth::user()->getConfig();
        $sede = $config['sedes'][0];

        $employes = Employes::where('id_sede',$sede)->get();
        foreach($employes as $value){
            foreach($fullData["fechas"] as $v){
                $today = Carbon::parse($v);
                // dd($today->format('Y-m-d'));
                $asistencia = Asistencia::where('id_employe',$value->id)
                ->where('id_aux_treg','!=',10)//dont get fault records
                ->where('id_aux_treg','!=',6)//dont get cese records
                ->whereNull('deletedAt')
                ->whereDate('created_at','<=',$today->toDateString())
                ->orderBy('created_at','desc')->first();
                // dd($asistencia);
                // where('id_employe',$employes->id)->whereDate('created_at',$v);
                if($asistencia){
                    // dd($asistencia);
                    $lastDate = new Carbon($asistencia->created_at);//the last date
                    $days_without_recorders = $lastDate->setHours(0)->setMinutes(0)->setSeconds(0)->diffInDays(Carbon::parse($v)->setHours(0)->setMinutes(0)->setSeconds(0)->toDateTimeString());//days from the last recorder until now
                    // dd($asistencia);
                    if($days_without_recorders>0){//check count days
                       //then set falta in this date
                        $holiday = Holidays::where('day',Carbon::parse($v)->day)->where('month',Carbon::parse($v)->month)->first();
                        //check if is saturday and check if got 6 assistances
                        //else if check holiday
                        if($holiday){
                            $asistencia = Asistencia::where('id_employe',$value->id)->where('id_aux_treg',10)->whereDate('created_at',$today->toDateString())->forceDelete();
                            $asistencia = Asistencia::where('id_employe',$value->id)->where('id_aux_treg',11)->whereDate('created_at',$today->toDateString())->forceDelete();
                                $asistencia = new Asistencia();
                                $asistencia->id_employe = $value->id;
                                $asistencia->id_aux_treg = 11;//holiday
                                $asistencia->id_employe_type = $value->id_employe_type;
                                $asistencia->id_sede = $value->id_sede;
                                $asistencia->dir_ind = $value->dir_ind;
                                $asistencia->type = $value->type;
                                $asistencia->turno = $value->turno;
                                $asistencia->id_function = $value->id_function;
                                $asistencia->id_proceso = $value->id_proceso;
                                $asistencia->paga = $value->remuneracion * 2;
                                $asistencia->created_at = Carbon::parse($v)->setHours(8)->format('Y-m-d H:i:s');
                                $asistencia->deleted_at = Carbon::parse($v)->setHours(17)->format('Y-m-d H:i:s');
                                $asistencia->save();
                            //update or create
                            // $asistencia = Asistencia::updateOrCreate(["id_aux_treg"=>11],[]);
                        }else{
                            // dd($asistencia);
                            //set falta today
                            //obtengo el ultimo dia de registro de asistencia
                            //todos los dias apartir de ese registro hasta la fecha actual de ejecucion de este script se deberan registrar
                            //como faltas, este script se ejecutara solo en la importacion de ingresos y en el servidor de manera diaria pero solo de un dia
                            $asistencia = Asistencia::where('id_employe',$value->id)->where('id_aux_treg',10)->whereDate('created_at',$today->toDateString())->forceDelete();
                            // dd($asistencia);
                            // if(count($asistencia) == 0){
                                $asistencia = new Asistencia();
                                $asistencia->id_employe = $value->id;
                                $asistencia->id_aux_treg = 10;//falta
                                $asistencia->id_employe_type = $value->id_employe_type;
                                $asistencia->id_sede = $value->id_sede;
                                $asistencia->dir_ind = $value->dir_ind;
                                $asistencia->type = $value->type;
                                $asistencia->turno = $value->turno;
                                $asistencia->id_function = $value->id_function;
                                $asistencia->id_proceso = $value->id_proceso;
                                $asistencia->paga = 0;
                                $asistencia->created_at = Carbon::parse($v)->setHours(1)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s');
                                $asistencia->deleted_at = Carbon::parse($v)->setHours(23)->setMinutes(59)->setSeconds(59)->format('Y-m-d H:i:s');
                                $asistencia->save();
                            // dd($asistencia);
                            // }
                        }
                    }
                }
            }
        }
    }

    public function loadlistworker($date,$id_user,$type,$sede,$id_proceso){
        $functions = json_decode(User::find($id_user)->user_group[0]->pivot->show_function);

        switch ($type) {
            case 'V':
                # code...Verificados
                $data = Asistencia::whereDate('created_at',Carbon::parse($date)->toDateString())->where('id_aux_treg',1)->whereIn('id_function',$functions)->where('id_sede',$sede)->where('id_user_checked',$id_user)->get();
                break;
            case 'SV':
                # code...Sin Verificar
                $data = Asistencia::whereDate('created_at',Carbon::parse($date)->toDateString())->where('id_aux_treg',1)->whereIn('id_function',$functions)->where('id_sede',$sede)->where('checked',0)->get();
                break;
            case 'SM':
                # code...
                $data = DB::table('reg_unchecked')
                ->join( 'employes' ,function ($join){
                    $join->on('reg_unchecked.reg_code','=','employes.code');
                    $join->orOn('reg_unchecked.reg_code','=','employes.doc_num');
                })
                ->join('funct_area','employes.id_function','funct_area.id')
                ->join('areas','funct_area.id_area','areas.id')
                ->where('reg_unchecked.id_user',$id_user)
                ->whereDate('reg_unchecked.created_at',Carbon::parse($date)->toDateString())
                ->select('employes.*','funct_area.description as funcion','areas.area as area')->get();
                break;
            case 'I':
                # code...
                $data = Asistencia::whereDate('created_at',Carbon::parse($date)->toDateString())
                ->where('id_aux_treg',1)
                ->whereNotIn('id_function',$functions)
                ->where('id_user_checked',$id_user)
                ->where('id_sede',$sede)->get();
                break;
            case 'SR':
                # code...
                $data = \DB::table('reg_employes')->where('id_user',$id_user)->whereDate('created_at',Carbon::parse($date)->toDateString())->get();
                break;
            default:
                # code...
                $data = [];
                break;
        }
        // dd(User::find($id_user)->user_group[0]->pivot->show_function);

        // dd($workers_checked);
        $datos["response"] = view('assistance.response-workers',compact('data','type'))->render();
        // $datos["title"] = "TITULO PROVICIONAL";
        return response()->json($datos,200,[]);
    }

    public function loadUsers($date,$id_proceso,$sede){
        $users = User::whereHas('user_group',function($query) use($sede){
            $query->where('id_group','=',19);
            //after discomment this code line
            $query->where('sedes','like','%'.$sede.'%');
        })->get();
        return view('assistance.response_users',compact('users','date','id_proceso','sede'));
    }
}
