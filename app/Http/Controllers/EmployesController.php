<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Asistencia;
// use App\Auxiliar\TypeReg;
use App\Models\Employes;
use App\Models\Funcion;
use App\Models\Auxiliar\TypeReg;
use App\Models\User;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $config;
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $config = Auth::user()->getConfig();
        // $config = Auth::user()->getConfig();
        if (Auth::user()->hasGroupPermission("viewAsistencia")) {
            return redirect()->route('assistance');
        }elseif(Auth::user()->hasGroupPermission("viewRGerencia")) {
            return view('gerencia.gerencia');
        }elseif(Auth::user()->hasGroupPermission("viewRGerenciaGeneral")) {
            return view('gerencia.gerencia_general');
        }elseif(Auth::user()->hasGroupPermission("viewRGerenciaRecursos")) {
            // return view('gerencia.gerencia_produccion');
            return view('gerencia.gerencia_recursos');
            // return view('gerencia.portada');
        }elseif(Auth::user()->hasGroupPermission("viewRGerenciaProduccion")){
            return view('gerencia.gerencia_produccion');
        }elseif(Auth::user()->hasGroupPermission("viewValidaciones")){
            $this->config = Auth::user()->getConfig();
            //this view is for user with one sede
            // if($sede==0){
            $sede = $this->config['sedes'][0];
            // }
            $users = User::whereHas('user_group',function($query) use($sede){
                $query->where('id_group','=',19);
                //after discomment this code line
                $query->where('sedes','like','%'.$sede.'%');
            })->get();
            return view('assistance.validaciones',compact('users'));
        }

        return view('home',compact('config'));
    }

    public function showQuery(){
        return view('consultas.index');
    }

    public function loadGraphics($start, $end,$sede){
        $this->config = Auth::user()->getConfig();
        $area = $this->config['areas'];
        $tregistro = $this->config['treg'];

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);

        $diff = $fecha1->diff($fecha2);
        // $config = Auth::user()->getConfig();
        // return $config;
        $treg = TypeReg::withTrashed()->whereIn('id',$tregistro)->orderBy('id','asc')->get();
        $qry = "";
        $label = array();
        $newData = [];
        $netoData = [];
        foreach ($treg as $key => $value) {
            # code...
            // $label["label"][0][$key] = $value->description;
            $newData['tag'][$key] = $value->description;
            $newData["hidden"][$key] = !in_array($value->id,$this->config['treg']);
            $qry .= "case id_aux_treg when ".$value->id." then 1 else 0 end as ".$value->description.",";
            $netoData[$value->id] = 0;
        }
        $qry .= 'date(created_at) as created_at';

        $data = Asistencia::whereHas('employes',function($query) use ($area){
            $query->whereHas('funcion',function($query1) use ($area){
                $query1->whereIn('id_area',$area);
            });
        })->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])
        ->whereIn('id_aux_treg',$tregistro)
        ->whereIn('id_sede',$sede)
        ->select(
                    DB::raw($qry),
                )
        ->orderBy(DB::raw('date(created_at)', 'desc'))->get()->toArray();
        //to array
        $day = "";
        $week = "";
        $month = "";
        //add here if new value is register
        $contador = 0;
        $bool = true;

        if ($diff->days == 0) {
            //dia ordenado por horas 00,03,06,09,12,3,6,9,00
            foreach ($data as $key => $value) {
                // $dataTime = DateTime::createFromFormat('Y-m-d H:i:s', $date->created_at);
                $dataTime = Carbon::createFromFormat('Y-m-d H:i:s', $value["created_at"], new DateTimeZone('America/Lima'));

                if ($bool) {
                    $newData["label"][$contador] = $dataTime->translatedFormat('l/d');
                    $day = $dataTime->format('D');
                    $bool = false;
                }

                if ($day == $dataTime->format('D')) { //05 - 04

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                } else {

                    $contador++;

                    $day = $dataTime->format('D');

                    $newData["label"][$contador] = $dataTime->translatedFormat('l/d');

                    foreach ($treg as $u => $p) {
                        # code...
                        $netoData[$p->id] = 0;
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                }
            }

        } elseif ($diff->days > 0 and $diff->days < 7) {
            //semana ordenado por dias L,MA,MI,J,V,S,D
            foreach ($data as $key => $value) {
                // $dataTime = DateTime::createFromFormat('Y-m-d H:i:s', $date->created_at);
                $dataTime = Carbon::createFromFormat('Y-m-d H:i:s', $value["created_at"], new DateTimeZone('America/Lima'));

                if ($bool) {
                    $newData["label"][$contador] = $dataTime->translatedFormat('l/d');
                    $day = $dataTime->format('D');
                    $bool = false;
                }

                if ($day == $dataTime->format('D')) { //25[] | 27[]

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                    //ok 25 [0]
                } else {
                    //ok 27[1]

                    $contador++;

                    $day = $dataTime->format('D');

                    $newData["label"][$contador] = $dataTime->translatedFormat('l/d');

                    foreach ($treg as $u => $p) {
                        # code...
                        $netoData[$p->id] = 0;
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                }
            }
        } elseif ($diff->days > 7 and $diff->days < 31) {
            //mes ordenado por semanas W1,W2,W3,W4
            foreach ($data as $key => $value) {
                // $dataTime = DateTime::createFromFormat('Y-m-d H:i:s', $date->created_at);
                $dataTime = Carbon::createFromFormat('Y-m-d H:i:s', $value["created_at"], new DateTimeZone('America/Lima'));

                if ($bool) {
                    $newData["label"][$contador] = "Semana N°" . $dataTime->format('W');
                    $week = $dataTime->format('W');
                    $bool = false;
                }

                if ($week == $dataTime->format('W')) { //05 - 04

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                } else {
                    $contador++;

                    $week = $dataTime->format('W');

                    $newData["label"][$contador] = "Semana N°" . $dataTime->format('W');

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                }
            }
        } elseif ($diff->days > 31 and $diff->days < 365) {
            //año ordenado por meses E,F,MAR,AB,MAY,JUN,JUL,AGO,SEP,OCTU,NOVI,DICI
            foreach ($data as $key => $value) {
                // $dataTime = DateTime::createFromFormat('Y-m-d H:i:s', $date->created_at);
                $dataTime = Carbon::createFromFormat('Y-m-d H:i:s', $value["created_at"], new DateTimeZone('America/Lima'));

                if ($bool) {
                    $newData["label"][$contador] = "Mes :" . $dataTime->translatedFormat('M');
                    $month = $dataTime->format('M');
                    $bool = false;
                }

                if ($month == $dataTime->format('M')) { //05 - 04

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                } else {
                    $contador++;

                    $month = $dataTime->format('M');

                    $newData["label"][$contador] = "Mes: " . $dataTime->translatedFormat('M');

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                }
            }
        }

        if (count($data) > 0) {
            $newData['fechas'] = [strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))];
            $newData['success'] = true;
            $newData['message'] = "Datos cargados";
            $newData['title'] = "Cargado";
            $newData['icon'] = "success";
            $newData['total'] = array_sum($netoData);
        } else {
            $newData['success'] = false;
            $newData['fechas'] = [strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))];
            $newData['message'] = "No se encontraron datos";
            $newData['title'] = "Cargado";
            $newData['icon'] = "warning";
            $newData['total'] = 0;
        }

        return response()->json($newData, 200, []);
    }

    public function loadPieGraphics($start,$end,$sede){
        $this->config = Auth::user()->getConfig();
        $area = $this->config['areas'];


        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }
        $result = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        $areas = Area::whereIn('id',$area)->get();//todas las areas;
        $total = 0;
        $total_c = 0;
        foreach ($areas as $key => $value) {
            # code...
            $asistencia = Asistencia::whereHas('funcion',function($query) use ($value){
                $query->where('id_area','=',$value->id);
            })->whereIn('id_sede',$sede)->where('id_aux_treg','=',1)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->select(DB::raw('count(*) as asistencia_total'),DB::raw('coalesce(sum(paga),0) as costos'))->first();
            // return response()->json($asistencia, 200, $headers);
            // return  $asistencia;
            $result['labels'][$key] = $value->area;
            $result['data'][$key] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
            $result['data_costo'][$key] = (isset($asistencia->costos))?$asistencia->costos:0;
            $result['backgroundcolor'][$key] = $value->color;
            $result['hidden'][$key] = !in_array($value->id,$this->config['areas']);
            $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
            $total_c += (isset($asistencia->costos))?$asistencia->costos:0;

        }
        $result['success'] = true;
        $result['total'] = $total;
        $result['total_c'] = $total_c;

        return response()->json($result, 200, []);
    }

    public function loadGraphicsbyArea($area,$start,$end,$sede){
        $this->config = Auth::user()->getConfig();
        $tregistro = $this->config["treg"];
        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);

        $diff = $fecha1->diff($fecha2);

        $treg = TypeReg::withTrashed()->whereIn('id',$tregistro)->orderBy('id','asc')->get();
        $qry = "";
        $label = array();
        $newData = [];
        $netoData = [];
        foreach ($treg as $key => $value) {
            # code...
            // $label["label"][0][$key] = $value->description;
            $newData['tag'][$key] = $value->description;
            $newData["hidden"][$key] = !in_array($value->id,$this->config['treg']);
            $qry .= "case id_aux_treg when ".$value->id." then 1 else 0 end as ".$value->description.",";
            $netoData[$value->id] = 0;
        }
        $qry .= 'date(created_at) as created_at';

        $data = Asistencia::whereHas('funcion',function($query) use ($area){
                $query->where('id_area','=',$area);
        })->whereIn('id_sede',$sede)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
        ->select(
            DB::raw($qry),
            )->orderBy(DB::raw('date(created_at)', 'desc'))->get();

        $day = "";
        $week = "";
        $month = "";
        (float) $neto_asistencia = 0;
        (float) $neto_licencia = 0;//licencia sin goze
        (float) $neto_licencia_c = 0;//licencia con goze
        (float) $neto_libre = 0;
        (float) $neto_permiso = 0;
        (float) $neto_permiso_c = 0;
        (float) $neto_cese = 0;
        //add here if new value is register
        $contador = 0;
        $bool = true;
        // $newData["hidden"][0] = !in_array(1,$this->config['treg']);
        // $newData["hidden"][1] = !in_array(2,$this->config['treg']);
        // $newData["hidden"][2] = !in_array(3,$this->config['treg']);
        // $newData["hidden"][3] = !in_array(7,$this->config['treg']);
        // $newData["hidden"][4] = !in_array(4,$this->config['treg']);
        // $newData["hidden"][5] = !in_array(5,$this->config['treg']);
        // $newData["hidden"][6] = !in_array(6,$this->config['treg']);

        if ($diff->days == 0) {
            //dia ordenado por horas 00,03,06,09,12,3,6,9,00
            foreach ($data as $key => $value) {
                // $dataTime = DateTime::createFromFormat('Y-m-d H:i:s', $date->created_at);
                $dataTime = Carbon::createFromFormat('Y-m-d H:i:s', $value["created_at"], new DateTimeZone('America/Lima'));

                if ($bool) {
                    $newData["label"][$contador] = $dataTime->translatedFormat('l/d');
                    $day = $dataTime->format('D');
                    $bool = false;
                }

                if ($day == $dataTime->format('D')) { //05 - 04

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                } else {

                    $contador++;

                    $day = $dataTime->format('D');

                    $newData["label"][$contador] = $dataTime->translatedFormat('l/d');

                    foreach ($treg as $u => $p) {
                        # code...
                        $netoData[$p->id] = 0;
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                }
            }

        } elseif ($diff->days > 0 and $diff->days < 7) {
            //semana ordenado por dias L,MA,MI,J,V,S,D
            foreach ($data as $key => $value) {
                // $dataTime = DateTime::createFromFormat('Y-m-d H:i:s', $date->created_at);
                $dataTime = Carbon::createFromFormat('Y-m-d H:i:s', $value["created_at"], new DateTimeZone('America/Lima'));

                if ($bool) {
                    $newData["label"][$contador] = $dataTime->translatedFormat('l/d');
                    $day = $dataTime->format('D');
                    $bool = false;
                }

                if ($day == $dataTime->format('D')) { //25[] | 27[]

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                    //ok 25 [0]
                } else {
                    //ok 27[1]

                    $contador++;

                    $day = $dataTime->format('D');

                    $newData["label"][$contador] = $dataTime->translatedFormat('l/d');

                    foreach ($treg as $u => $p) {
                        # code...
                        $netoData[$p->id] = 0;
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                }
            }
        } elseif ($diff->days > 7 and $diff->days < 31) {
            //mes ordenado por semanas W1,W2,W3,W4
            foreach ($data as $key => $value) {
                // $dataTime = DateTime::createFromFormat('Y-m-d H:i:s', $date->created_at);
                $dataTime = Carbon::createFromFormat('Y-m-d H:i:s', $value["created_at"], new DateTimeZone('America/Lima'));

                if ($bool) {
                    $newData["label"][$contador] = "Semana N°" . $dataTime->format('W');
                    $week = $dataTime->format('W');
                    $bool = false;
                }

                if ($week == $dataTime->format('W')) { //05 - 04

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                } else {
                    $contador++;

                    $week = $dataTime->format('W');

                    $newData["label"][$contador] = "Semana N°" . $dataTime->format('W');

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                }
            }
        } elseif ($diff->days > 31 and $diff->days < 365) {
            //año ordenado por meses E,F,MAR,AB,MAY,JUN,JUL,AGO,SEP,OCTU,NOVI,DICI
            foreach ($data as $key => $value) {
                // $dataTime = DateTime::createFromFormat('Y-m-d H:i:s', $date->created_at);
                $dataTime = Carbon::createFromFormat('Y-m-d H:i:s', $value["created_at"], new DateTimeZone('America/Lima'));

                if ($bool) {
                    $newData["label"][$contador] = "Mes :" . $dataTime->translatedFormat('M');
                    $month = $dataTime->format('M');
                    $bool = false;
                }

                if ($month == $dataTime->format('M')) { //05 - 04

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                } else {
                    $contador++;

                    $month = $dataTime->format('M');

                    $newData["label"][$contador] = "Mes: " . $dataTime->translatedFormat('M');

                    foreach ($treg as $u => $p) {
                        # code...
                        $newData["data"][$u][$contador] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                        // $newData["hidden"][0] = !in_array(1,$this->config);
                        $netoData[$p->id] = (float) $netoData[$p->id] + (float) $value[$p["description"]];
                    }
                }
            }
        }

        if (count($data) > 0) {
            // $newData['total'] = $total_asistencia;
            $newData['success'] = true;
        } else {
            // $newData['total'] = $total_asistencia;
            $newData['success'] = false;
        }

        return response()->json($newData, 200, []);
    }

    public function loadGraphicsbyAreaML($area,$start,$end,$sede){
        $this->config = Auth::user()->getConfig();

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);

        $diff = $fecha1->diff($fecha2);

        $funciones = Funcion::where('id_area','=',$area)->get();
        // $coleccion = [0];
        // foreach ($funciones as $key => $value) {
        //     # code...
        //     $coleccion[] = $value->id;
        // }

        // $data = DB::select('select count(reg_assistance.id_employe) as total,funct_area.description,DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d") as created_at from reg_assistance inner join employes on reg_assistance.id_employe = employes.id inner join funct_area on employes.id_function = funct_area.id where employes.id_function in '.str_replace(['[',']'],['(',')'], json_encode($coleccion)).' GROUP by employes.id_function,funct_area.description,DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d") order by DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d") desc,funct_area.description desc');
        // return $data;
        $newData = [];
        for ($i=0; $i < $diff->days +1; $i++) {
            # code...
            $dataTime = Carbon::parse($start)->addDays($i);
            $day = "";
            $contador = 0;
            $bool = true;
            $newData['label'][$i] = $dataTime->format('Y-m-d');

            foreach ($funciones as $key => $value) {
                # code...
                $data = collect(DB::select('select count(reg_assistance.id_employe) as total,funct_area.description,DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d") as created_at from reg_assistance inner join employes on reg_assistance.id_employe = employes.id inner join funct_area on employes.id_function = funct_area.id where employes.id_function = '.$value->id.' and DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d") = "'.$dataTime->format('Y-m-d').'" and reg_assistance.id_aux_treg = 1 GROUP by employes.id_function,funct_area.description,DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d") order by DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d") desc,funct_area.description desc'))->first();
                $newData['data'][$key][$i] = (isset($data->total)?$data->total:0);
                $newData['tag'][$key] = $value->description;
                $newData['color'][$key] = "red";
            }

        }

        // if (count($data) > 0) {
        //     $newData['success'] = true;
        // } else {
        //     $newData['success'] = false;
        // }

        return response()->json($newData, 200, []);
    }

    public function loadGraphicsbyType($start,$end,$sede,$checked = 0, $time = "ALL",$proceso = "ALL"){
        $this->config = Auth::user()->getConfig();

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        if($proceso === "ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$this->config['sedes'])->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();

        }else{
            $proceso = [$proceso];
        }

        $areas = $this->config['areas'];
        $result = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);

        $type = Employes::select('type')->groupBy('type')->orderBy('type','desc')->get();//todas las areas;

        $total = 0;
        if($checked==1){
            foreach ($type as $key => $value) {
                # code...
                $asistencia = Asistencia::whereHas('funcion',function($query) use ($value,$areas){
                        $query->whereIn('id_area',$areas);
                })->whereIn('id_sede',$sede)
                ->where('id_aux_treg','=',1)
                ->where('type',$value->type)
                ->whereIn('turno',($time === "ALL" ? ["DIA","NOCHE"]:[$time]))
                ->whereIn('id_proceso',$proceso)
                ->whereNotNull('id_user_checked')
                ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                ->select(DB::raw('count(*) as asistencia_total'))
                ->first();
                // return response()->json($asistencia, 200, $headers);
                // return  $asistencia;
                $result['labels'][$key] = $value->type;
                $result['data'][$key] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                // $result['backgroundcolor'][$key] = $value->color;
            }
        }else{
            foreach ($type as $key => $value) {
                # code...
                $asistencia = Asistencia::whereHas('funcion',function($query) use ($value,$areas){
                        $query->whereIn('id_area',$areas);
                })->whereIn('id_sede',$sede)
                ->where('id_aux_treg','=',1)
                ->where('type',$value->type)
                ->whereIn('id_proceso',$proceso)
                ->whereIn('turno',($time === "ALL" ? ["DIA","NOCHE"]:[$time]))
                // ->whereNotNull('id_user_checked')
                ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                ->select(DB::raw('count(*) as asistencia_total'))
                ->first();
                // return response()->json($asistencia, 200, $headers);
                // return  $asistencia;
                $result['labels'][$key] = $value->type;
                $result['data'][$key] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                // $result['backgroundcolor'][$key] = $value->color;
            }
        }
        $result['total'] = $total;
        $result['success'] = true;

        return response()->json($result, 200, []);
    }

    public function loadGraphicsOld($start, $end ,$sede){
        Carbon::setLocale('es_PE');
        $this->config = Auth::user()->getConfig();

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        // return $fecha2->format('Y-m-d H:i');
        $diff = $fecha1->diffInDays($fecha2);
        // return $config;
        $treg = TypeReg::withTrashed()->orderBy('id','asc')->get();
        $newData = [];

        if($diff == 0){
            $group = "day(created_at)";
            $tf = 'l/d';
            $format = "d";
        }elseif($diff > 0 and $diff < 7){
            $group = "day(created_at)";
            $tf = 'l/d';
            $format = "d";
        } elseif ($diff > 7 and $diff < 31){
            $group = "YEARWEEK(created_at)";
            $diff = $fecha1->diffInWeeks($fecha2);
            $tf = 'w';
            $format = null;
        } elseif ($diff > 31 and $diff < 365){
            $group = "month(created_at)";
            $diff = $fecha1->diffInMonths($fecha2);
            $tf = 'M';
            $format = "m";

        }elseif($diff > 365){
            $group = "year(created_at)";
            $diff = $fecha1->diffInMonths($fecha2);
            $tf = 'Y';
            $format = "Y";
        }

        $contador = 0;
        $array_posicion = array();
        foreach ($treg as $key => $value) {
            # code...
            $contador = 0;


            $data = Asistencia::whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->where("id_aux_treg",'=',$value->id)
            ->whereIn("id_sede",$sede)
            ->select(DB::raw('count(id_aux_treg) as total'), DB::raw($group. ' as fecha'), DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i")) as created_at'))
            ->groupBy(DB::raw($group),'id_aux_treg','id_sede')
            ->orderBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'),'asc')->get();

            // $datos = Asistencia::whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            // ->where("id_aux_treg",'=',$value->id)
            // ->whereIn("id_sede",$sede)
            // ->select(DB::raw('count(id_aux_treg) as total'), DB::raw($group. ' as fecha'), DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i")) as created_at'))
            // ->groupBy(DB::raw($group))
            // ->orderBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'),'desc')->toSql();

            $newData['tag'][$key] = $value->description;
            $newData['hidden'][$key] = !in_array($value->id,$this->config['treg']);
            // $newData['datos'][$key] = $value->created_at;
            // $newData['data'][$key][0] = 0;
            foreach ($data as $k => $v) {
                # code...
                //cambiar esto si en caso manda error
                //cambiar esto si en caso manda error
                if($format){
                    $newData['label'][$v->created_at->format($format)] = $v->created_at->translatedFormat($tf);
                }else{
                    $newData['label'][$v->fecha] = "Semana " . ($v->fecha + 1);
                }
                // $contador++;
            }

            for ($i=0; $i <= $diff; $i++) {
                $array_posicion[$i] = $i;
                // $fecha = Carbon::parse($start)->addDays($i)->translatedFormat($tf);
                // # code...
                // $newData['label'][$i] = $fecha;
                $newData['data'][$key][$i] = (isset($newData['data'][$key][$i]))?$newData['data'][$key][$i]:0;
            }
            //check array for null or void values and put zero

        }

        if (isset($newData['label'])) {
            $newData['label'] = array_values($newData['label']);
            $newData['fechas'] = [strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))];
            $newData['success'] = true;
            $newData['message'] = "Datos cargados";
            $newData['title'] = "Cargado";
            $newData['icon'] = "success";
        } else {
            $newData['success'] = false;
            $newData['fechas'] = [strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))];
            $newData['message'] = "No se encontraron datos";
            $newData['title'] = "Cargado";
            $newData['icon'] = "warning";
        }

        return response()->json($newData, 200, []);
    }

    public function loadGraphicsbyAreOld($area,$start, $end ,$sede){
        Carbon::setLocale('es_PE');
        $this->config = Auth::user()->getConfig();

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        // return $fecha2->format('Y-m-d H:i');
        $diff = $fecha1->diffInDays($fecha2);
        // return $config;
        $treg = TypeReg::withTrashed()->orderBy('id','asc')->get();
        $newData = [];

        if($diff == 0){
            $group = "day(created_at)";
            $tf = 'l/d';
            $format = "d";
        }elseif($diff > 0 and $diff < 7){
            $group = "day(created_at)";
            $tf = 'l/d';
            $format = "d";
        } elseif ($diff > 7 and $diff < 31){
            $group = "YEARWEEK(created_at)";
            $diff = $fecha1->diffInWeeks($fecha2);
            $tf = 'w';
            $format = null;
        } elseif ($diff > 31 and $diff < 365){
            $group = "month(created_at)";
            $diff = $fecha1->diffInMonths($fecha2);
            $tf = 'M';
            $format = "m";

        }elseif($diff > 365){
            $group = "year(created_at)";
            $diff = $fecha1->diffInMonths($fecha2);
            $tf = 'Y';
            $format = "Y";
        }

        $contador = 0;
        foreach ($treg as $key => $value) {
            # code...
            // $contador = 0;
            $data = Asistencia::whereHas('employes',function($query) use ($area){
                $query->whereHas('funcion',function($query2) use ($area){
                    $query2->where('id_area','=',$area);
                });
            })->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->where("id_aux_treg",'=',$value->id)
            ->whereIn("id_sede",$sede)
            ->select(DB::raw('count(id_aux_treg) as total'), DB::raw($group. ' as fecha'), DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i")) as created_at'))
            ->groupBy(DB::raw($group),'id_aux_treg','id_sede')
            ->orderBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'),'asc')->get();

            // $datos = Asistencia::whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            // ->where("id_aux_treg",'=',$value->id)
            // ->whereIn("id_sede",$sede)
            // ->select(DB::raw('count(id_aux_treg) as total'), DB::raw($group. ' as fecha'), DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i")) as created_at'))
            // ->groupBy(DB::raw($group))
            // ->orderBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'),'desc')->toSql();

                $newData['tag'][$key] = $value->description;
                $newData['hidden'][$key] = !in_array($value->id,$this->config['treg']);
                $newData['datos'][$key] = $value->created_at;
                foreach ($data as $k => $v) {
                    # code...
                    //cambiar esto si en caso manda error
                                    //cambiar esto si en caso manda error
                if($format){
                    $newData['label'][$v->created_at->format($format)] = ($format)?$v->created_at->translatedFormat($tf): "Semana " . ($v->fecha + 1);
                }else{
                    $newData['label'][$v->fecha] = ($format)?$v->created_at->translatedFormat($tf): "Semana " . ($v->fecha + 1);
                }
                $newData['data'][$key][$k] = $v->total;
                // $contador++;
            }
            // Carbon::now()->setMonth()
            //check array for null or void values and put zero
            for ($i=0; $i <= $diff; $i++) {
                // $fecha = Carbon::parse($start)->addDays($i)->translatedFormat($tf);
                // # code...
                // $newData['label'][$i] = $fecha;
                $newData['data'][$key][$i] = (isset($newData['data'][$key][$i]))?$newData['data'][$key][$i]:0;
            }
        }

        if (isset($newData['label'])) {
            $newData['label'] = array_values($newData['label']);
            $newData['fechas'] = [strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))];
            $newData['success'] = true;
            $newData['message'] = "Datos cargados";
            $newData['title'] = "Cargado";
            $newData['icon'] = "success";
        } else {
            $newData['success'] = false;
            $newData['fechas'] = [strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))];
            $newData['message'] = "No se encontraron datos";
            $newData['title'] = "Cargado";
            $newData['icon'] = "warning";
        }

        return response()->json($newData, 200, []);
    }

    public function loadGraphicsbyTypeArea($areas,$start,$end,$sede){
        // $this->config = Auth::user()->getConfig();
        // $areas = $this->config['areas'];
        $this->config = Auth::user()->getConfig();

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        $result = array();
        $fecha1 = Carbon::parse($start)->format('Y-m-d H:i');
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59)->format('Y-m-d H:i');

        $type = Employes::select('type')->groupBy('type')->get();//todas las areas;
        // return $type;
        $total = 0;
        foreach ($type as $key => $value) {
            # code...
            $asistencia = Asistencia::whereHas('funcion',function($query) use ($value,$areas){
                    $query->where('id_area','=',$areas);
            })->where('type',$value->type)->where('id_aux_treg','=',1)->whereIn('id_sede',$sede)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1), strtotime($fecha2)])->select(DB::raw('count(*) as asistencia_total'))->first();
            // return response()->json($asistencia, 200, $headers);
            // return  $asistencia;
            $result['fechas'] =  $fecha1 . " - ". $fecha2;
            $result['asistencia'][$key] = $asistencia;
            $result['labels'][$key] = $value->type;
            $result['data'][$key] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
            $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
            // $result['backgroundcolor'][$key] = $value->color;
        }
        $result['total'] = $total;
        $result['success'] = true;

        return response()->json($result, 200, []);
    }

    public function loadGraphicsbyIndDir($start,$end,$sede,$checked = false,$time = "ALL",$proceso = "ALL"){
        $this->config = Auth::user()->getConfig();

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        if($proceso === "ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$sede)->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();
        }else{
            $proceso = [$proceso];
        }

        $areas = $this->config['areas'];
        $result = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);

        $type = Employes::select('dir_ind')->groupBy('dir_ind')->orderBy('dir_ind','asc')->get();//todas las areas;

        $total = 0;
        if($checked=="true"){
            foreach ($type as $key => $value) {
                # code...
                $asistencia = Asistencia::whereHas('funcion',function($query) use ($value,$areas){
                        $query->whereIn('id_area',$areas);
                })->where('id_aux_treg','=',1)
                ->where('dir_ind',$value->dir_ind)
                ->whereIn('id_proceso',$proceso)
                ->whereIn('id_sede',$sede)
                ->whereIn('turno',($time === "ALL" ? ["DIA","NOCHE"]:[$time]))
                ->whereNotNull('id_user_checked')
                ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                ->select(DB::raw('count(*) as asistencia_total'))
                ->first();
                // return response()->json($asistencia, 200, $headers);
                // return  $asistencia;
                $result['labels'][$key] = $value->dir_ind;
                $result['data'][$key] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                // $result['backgroundcolor'][$key] = $value->color;
            }
        }else{
            foreach ($type as $key => $value) {
                # code...
                $asistencia = Asistencia::whereHas('funcion',function($query) use ($value,$areas){
                        $query->whereIn('id_area',$areas);
                })->where('id_aux_treg','=',1)
                ->where('dir_ind',$value->dir_ind)
                ->whereIn('id_proceso',$proceso)
                ->whereIn('id_sede',$sede)
                ->whereIn('turno',(empty($time) ? ["DIA","NOCHE"]:[$time]))
                // ->whereNotNull('id_user_checked')
                ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                ->select(DB::raw('count(*) as asistencia_total'))
                ->first();
                // return response()->json($asistencia, 200, $headers);
                // return  $asistencia;
                $result['labels'][$key] = $value->dir_ind;
                $result['data'][$key] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                // $result['backgroundcolor'][$key] = $value->color;
            }
        }


        $result['total'] = $total;
        $result['success'] = true;

        return response()->json($result, 200, []);
    }

    public function assistanceByUser($start,$end,$sede,$time = "ALL",$proceso = "ALL"){
        //PENDIENTE PARA MEJORAR LA FUNCION, YA NO SERA POR USUARIOS
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);

        if($proceso === "ALL"){
            // $proceso = \DB::table('areas_sedes')->where('id_sede',$sede)->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();
        }else{
            $proceso = [$proceso];
        }

        $users = User::whereHas('user_group',function($query) use($sede){
            $query->where('id_group','=',23);
            //after discomment this code line
            $query->where('sedes','like','%'.$sede.'%');
        })->get();

        $datos = [];

        $total = Asistencia::where('id_sede',$sede)
        ->whereIn('id_proceso',$proceso)
        ->whereIn('turno',($time === "ALL" ? ["DIA","NOCHE"]:[$time]))
        ->whereNull('deletedAt')
        ->where('id_aux_treg',1)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])->count();

        $verificados = Asistencia::where('checked',1)
        ->where('id_sede',$sede)
        ->whereIn('id_proceso',$proceso)
        ->whereIn('turno',($time === "ALL" ? ["DIA","NOCHE"]:[$time]))
        ->whereNull('deletedAt')
        ->where('checked',1)
        ->where('id_aux_treg',1)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])->count();

        $unchecked = DB::table('reg_unchecked')->whereIn('id_proceso',$proceso)->where('id_sede',$sede)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])->count();

        $unregister = DB::table('reg_employes')->where('sede_id',$sede)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])->count();

        // return response()->json($datos, 200, []);
        $datos["response"] = view('gerencia.response_users',compact('users','sede','fecha1','fecha2','time'))->render();
        $datos["total_v"] = $verificados;
        $datos["total_sm"] = $unchecked;
        $datos["total_sr"] = $unregister;
        $datos["total"] = $total;

        return response()->json($datos,200,[]);
    }

    public function loadGraphicsbyTypeUser($start,$end,$id_user,$time = null){
        // $this->config = Auth::user()->getConfig();
        $user = User::where('id','=',$id_user)->first();
        $sede = json_decode($user->user_group[0]->pivot->sedes);
        // $areas = json_decode($user->user_group[0]->pivot->show_areas);
        $funciones = json_decode($user->user_group[0]->pivot->show_function);
        // $areas = $this->config['areas'];

        $result = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);

        $type = Employes::select('type')->groupBy('type')->orderBy('type','desc')->get();//todas las areas;
        $total = 0;

        foreach ($type as $key => $value) {
            # code...
            // $asistencia = Asistencia::whereHas('funcion',function($query) use ($value,$areas){
            //         $query->whereIn('id_area',$areas);
            // })->where('type',$value->type)
            $asistencia = Asistencia::where('type',$value->type)
            // ->where('id_user_checked',$id_user)
            ->where('id_aux_treg',1)
            ->whereIn('id_sede',$sede)
            ->whereIn('id_function',$funciones)
            ->whereIn('turno',(empty($time) ? ["DIA","NOCHE"]:[$time]))
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->select(DB::raw('count(*) as asistencia_total'))
            ->first();
            // return response()->json($asistencia, 200, $headers);
            // return  $asistencia;
            $result['labels'][$key] = $value->type;
            $result['data'][$key] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
            $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
            // $result['backgroundcolor'][$key] = $value->color;
        }
        $result['total'] = $total;
        if ($total > 0) {
            $result['fechas'] = [strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))];
            $result['success'] = true;
            $result['message'] = "Datos cargados";
            $result['title'] = "Cargado";
            $result['icon'] = "success";
        } else {
            $result['success'] = false;
            $result['fechas'] = [strtotime($fecha1->format('Y-m-d H:i')),strtotime($fecha2->format('Y-m-d H:i'))];
            $result['message'] = "No se encontraron datos";
            $result['title'] = "Cargado";
            $result['icon'] = "warning";
        }

        return response()->json($result, 200, []);
    }

    public function loadGraphicsbyIndDirUser($start,$end,$id_user,$time = null){
        // $this->config = Auth::user()->getConfig();
        $user = User::where('id','=',$id_user)->first();
        $sede = json_decode($user->user_group[0]->pivot->sedes);
        $funciones = json_decode($user->user_group[0]->pivot->show_function);

        $result = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);

        $type = Employes::select('dir_ind')->groupBy('dir_ind')->orderBy('dir_ind','asc')->get();//todas las areas;

        $total = 0;
        foreach ($type as $key => $value) {
            # code...
            // $asistencia = Asistencia::whereHas('funcion',function($query) use ($value,$areas){
            //      $query->whereIn('id_area',$areas);
            // })->where('dir_ind',$value->dir_ind)
            $asistencia = Asistencia::where('dir_ind',$value->dir_ind)
            ->where('id_aux_treg','=',1)
            // ->where('id_user_checked',$id_user)
            ->whereIn('id_sede',$sede)
            ->whereIn('id_function',$funciones)
            ->whereIn('turno',(empty($time) ? ["DIA","NOCHE"]:[$time]))
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->select(DB::raw('count(*) as asistencia_total'))
            ->first();
            // return response()->json($asistencia, 200, $headers);
            // return  $asistencia;
            $result['labels'][$key] = $value->dir_ind;
            $result['data'][$key] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
            $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
            // $result['backgroundcolor'][$key] = $value->color;
        }
        $result['total'] = $total;
        $result['success'] = true;

        return response()->json($result, 200, []);
    }

    public function loadlistworker($start,$end,$id_user,$id_sede,$type,$time = "ALL",$proceso = "ALL"){
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);

        // dd(User::find($id_user)->user_group[0]->pivot->show_function);

        if($id_user!=0){
            $funciones = json_decode(User::find($id_user)->user_group[0]->pivot->show_function);
            //ya no sera necesario
            $workers_checked = Asistencia::whereIn('id_function',$funciones)
            ->where('checked',1)
            ->where('id_aux_treg',1)
            ->whereIn('turno',($time === "ALL" ? ["DIA","NOCHE"]:[$time]))
            ->whereNull('deletedAt')
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->get();

            $workers_checked_sm = DB::table('reg_unchecked')->where('id_user',$id_user)
            ->whereNull('deleted_at')
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->get();

            $workers_sv = null;

            $workers_ss = Asistencia::whereIn('id_function',$funciones)
            ->where('id_aux_treg',1)
            ->whereIn('turno',($time === "ALL" ? ["DIA","NOCHE"]:[$time]))
            ->whereNotNull('created_at')
            ->whereNull('deleted_at')
            ->whereNull('deletedAt')
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->get();


            $workers_sr =  DB::table('reg_employes')->where('id_user',$id_user)
            ->whereNull('deleted_at')
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->get();

        }else{
            $this->config = Auth::user()->getConfig();

            if($proceso === "ALL"){
                // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$this->config['sedes'])->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
                $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();

            }else{
                $proceso = [$proceso];
            }

            $workers_checked = Asistencia::where('checked',1)
            ->where('id_sede',$id_sede)
            ->whereIn('id_proceso',$proceso)
            ->whereNull('deletedAt')
            ->where('id_aux_treg',1)
            ->whereIn('turno',($time === "ALL" ? ["DIA","NOCHE"]:[$time]))
            ->whereNotNull('id_user_checked')
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->get();

            $workers_checked_sm = DB::table('reg_unchecked')->whereNull('deleted_at')
            ->where('id_sede',$id_sede)
            ->whereIn('id_proceso',$proceso)
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->get();

            $workers_sv = Asistencia::whereNull('deletedAt')
            ->where('id_sede',$id_sede)
            ->whereIn('id_proceso',$proceso)
            ->where('id_aux_treg',1)
            ->whereIn('turno',($time === "ALL" ? ["DIA","NOCHE"]:[$time]))
            ->whereNull('id_user_checked')
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->get();

            $workers_ss = null;

            $workers_sr =  DB::table('reg_employes')->where('sede_id',$id_sede)
            ->whereNull('deleted_at')
            ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->get();
        }

        return view('gerencia.response_workers',compact('workers_checked','workers_checked_sm','workers_sv','workers_ss','workers_sr','type'));
    }

    public function loadlistworkerbyArea($start,$end,$id_area,$id_sede,$type,$time= "ALL",$proceso = "ALL"){
        $this->config = Auth::user()->getConfig();
        $sede = $this->config["sedes"][0];//sede principal
        if($proceso === "ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$this->config['sedes'])->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();

        }else{
            $proceso = [$proceso];
        }
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);

        $funciones = \App\Funcion::where('id_area',$id_area)->get()->pluck('id');

        $workers_checked = Asistencia::whereIn('id_function',$funciones)
        ->where('checked',1)
        ->whereIn('id_proceso',$proceso)
        ->where('id_aux_treg',1)
        ->whereIn('turno',(empty($time) ? ["DIA","NOCHE"]:[$time]))
        ->where('id_sede',$sede)
        ->whereNull('deletedAt')
        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
        ->get();

        $workers_checked_sm = DB::table('reg_unchecked')
        ->whereNull('deleted_at')
        ->where('id_sede',$sede)
        ->whereIn('id_proceso',$proceso)
        ->whereBetween(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), [$fecha1->format('Y-m-d'), $fecha2->format('Y-m-d')])
        ->get();

        $workers_sv = Asistencia::whereNull('deletedAt')
        ->whereIn('id_function',$funciones)
        ->whereIn('id_proceso',$proceso)
        ->where('id_aux_treg',1)
        ->whereIn('turno',(empty($time) ? ["DIA","NOCHE"]:[$time]))
        ->whereNull('id_user_checked')
        ->where('id_sede',$sede)
        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
        ->get();

        $workers_ss = Asistencia::whereIn('id_function',$funciones)
        ->where('id_aux_treg',1)
        ->whereIn('id_proceso',$proceso)
        ->whereIn('turno',(empty($time) ? ["DIA","NOCHE"]:[$time]))
        ->whereNotNull('created_at')
        ->whereNull('deleted_at')
        ->whereNull('deletedAt')
        ->where('id_sede',$sede)
        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
        ->get();
        // $datos["response"] = ->render();
        // $datos["title"] = "TITULO PROVICIONAL";
        // return response()->json($datos,200,[]);
        return view('gerencia.response_workers',compact('workers_checked','workers_checked_sm','workers_sv','workers_ss','type'));
    }

    public function loadAssistanceByArea($start,$end,$sede,$id_user = 0,$checked = 0,$time = 0,$multiplicador = 3.401,$proceso = "ALL"){
        //asistance
        // dd($time);
        #the multiplier parameter works to calculate the kpi based on the value of the dollar from today
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        $datos = null;
        $datos2 = null;
        $config = Auth::user()->getConfig();
        // $area = $this->config['areas'];

        if($sede==0){
            $sedes = $config['sedes'];
        }else{
            $sedes = [$sede];
        }

        if($proceso === "ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$sedes)->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();
        }else{
            $proceso = [$proceso];
        }

        try{
            if($id_user!=0){
                $user = User::where('id','=',$id_user)->first();

                $sede = json_decode($user->user_group[0]->pivot->sedes);
                $areas = json_decode($user->user_group[0]->pivot->show_areas);
                // dd($time === 0 ? ["DIA","NOCHE"]:[$time]);
                $datos = Asistencia::select(
                'color',
                DB::raw('areas.id as id'),
                DB::raw('count(*) as total'),
                DB::raw('coalesce(sum(case when checked = 1 then 1 else 0 end),0) as verificados'),
                DB::raw('coalesce(sum(case when checked = 0 then 1 else 0 end),0) as no_verificados'),
                DB::raw('coalesce(sum(paga),0) as costos'),
                DB::raw('coalesce(sum(case when type = "JORNAL" then 1 else 0 end),0) as jornal'),
                DB::raw('coalesce(sum(case when type = "DESTAJO" then 1 else 0 end),0) as destajo'),
                DB::raw('coalesce(sum(case when dir_ind = "DIRECTO" then 1 else 0 end),0) as directo'),
                DB::raw('coalesce(sum(case when dir_ind = "INDIRECTO" then 1 else 0 end),0) as indirecto'),
                DB::RAW('areas.area as name'))
                ->join('funct_area','reg_assistance.id_function','funct_area.id')
                ->join('areas','funct_area.id_area','areas.id')
                // ->where('reg_assistance.id_user_checked','=',$user->id)
                ->where('reg_assistance.id_aux_treg','=',1)
                ->where('reg_assistance.id_sede',$sede)
                ->whereIn('funct_area.id_area',$areas)
                ->whereIn('turno',($time===0 ? ["DIA","NOCHE"]:[$time]))
                ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                ->groupBy('areas.area','areas.color','areas.id')
                ->orderBy('total','desc')
                ->get();
            }else{
                if(Auth::user()->hasGroupPermission('viewRGerenciaRecursos')){
                    //change this
                    if($checked){
                        $datos = Asistencia::select(
                        'areas.id as id_area',
                        'color',
                        DB::raw('count(*) as total_qty'),
                        DB::raw('coalesce(sum(paga),0) as total'),
                        DB::raw('coalesce(sum(case when type = "JORNAL" then 1 else 0 end),0) as jornal'),
                        DB::raw('coalesce(sum(case when type = "DESTAJO" then 1 else 0 end),0) as destajo'),
                        DB::raw('coalesce(sum(case when dir_ind = "DIRECTO" then 1 else 0 end),0) as directo'),
                        DB::raw('coalesce(sum(case when dir_ind = "INDIRECTO" then 1 else 0 end),0) as indirecto'),
                        DB::RAW('areas.area as name'))
                        ->join('funct_area','reg_assistance.id_function','funct_area.id')
                        ->join('areas','funct_area.id_area','areas.id')
                        // ->whereNotNull('reg_assistance.id_user_checked')
                        ->where('reg_assistance.id_aux_treg','=',1)
                        ->whereIn('id_proceso',$proceso)
                        ->whereIn('reg_assistance.id_sede',$sedes)
                        ->whereIn('turno',($time==0 ? ["DIA","NOCHE"]:[$time]))
                        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                        ->where('areas.first_out_group',1)
                        ->groupBy('areas.id')
                        // ->orderByRaw('FIELD (areas.id, ' . implode(', ', $ids) . ') DESC')
                        // ->orderBy('areas.area','desc')
                        // ->orderBy('total','desc')
                        ->get();
                        // dd($datos);
                        $datos2 = Asistencia::select(
                        'areas.id as id_area',
                        'color',
                        DB::raw('count(*) as total_qty'),
                        DB::raw('coalesce(sum(paga),0) as total'),
                        DB::raw('coalesce(sum(case when type = "JORNAL" then 1 else 0 end),0) as jornal'),
                        DB::raw('coalesce(sum(case when type = "DESTAJO" then 1 else 0 end),0) as destajo'),
                        DB::raw('coalesce(sum(case when dir_ind = "DIRECTO" then 1 else 0 end),0) as directo'),
                        DB::raw('coalesce(sum(case when dir_ind = "INDIRECTO" then 1 else 0 end),0) as indirecto'),
                        DB::RAW('areas.area as name'))
                        ->join('funct_area','reg_assistance.id_function','funct_area.id')
                        ->join('areas','funct_area.id_area','areas.id')
                        // ->whereNotNull('reg_assistance.id_user_checked')
                        ->where('reg_assistance.id_aux_treg','=',1)
                        ->whereIn('id_proceso',$proceso)
                        ->whereIn('reg_assistance.id_sede',$sedes)
                        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                        ->where('areas.first_out_group',0)
                        ->groupBy('areas.id')
                        // ->orderByRaw('FIELD (areas.id, ' . implode(', ', $ids) . ') DESC')
                        // ->orderBy('areas.area','desc')
                        // ->orderBy('total','desc')
                        ->get();
                        // dd($datos);
                    }else{
                        $datos = Asistencia::select(
                        'areas.id as id_area',
                        'color',
                        DB::raw('count(*) as total'),
                        DB::raw('coalesce(sum(paga),0) as total_money'),
                        DB::raw('coalesce(sum(case when type = "JORNAL" then 1 else 0 end),0) as jornal'),
                        DB::raw('coalesce(sum(case when type = "DESTAJO" then 1 else 0 end),0) as destajo'),
                        DB::raw('coalesce(sum(case when dir_ind = "DIRECTO" then 1 else 0 end),0) as directo'),
                        DB::raw('coalesce(sum(case when dir_ind = "INDIRECTO" then 1 else 0 end),0) as indirecto'),
                        DB::RAW('areas.area as name'))
                        ->join('funct_area','reg_assistance.id_function','funct_area.id')
                        ->join('areas','funct_area.id_area','areas.id')
                        // ->where('reg_assistance.id_user_checked','=',$user->id)
                        ->where('reg_assistance.id_aux_treg','=',1)
                        ->whereIn('id_proceso',$proceso)
                        ->whereIn('reg_assistance.id_sede',$sedes)
                        ->whereIn('turno',($time == 0 ? ["DIA","NOCHE"]:[$time]))
                        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                        ->where('areas.first_out_group',1)
                        ->groupBy('areas.id')
                        // ->orderBy('areas.area','desc')
                        // ->orderByRaw('FIELD (areas.id, ' . implode(', ', $ids) . ') DESC')
                        // ->orderBy('total','desc')
                        ->get();
                        // dd($datos);
                        $datos2 = Asistencia::select(
                        'areas.id as id_area',
                        'color',
                        DB::raw('count(*) as total'),
                        DB::raw('coalesce(sum(paga),0) as total_money'),
                        DB::raw('coalesce(sum(case when type = "JORNAL" then 1 else 0 end),0) as jornal'),
                        DB::raw('coalesce(sum(case when type = "DESTAJO" then 1 else 0 end),0) as destajo'),
                        DB::raw('coalesce(sum(case when dir_ind = "DIRECTO" then 1 else 0 end),0) as directo'),
                        DB::raw('coalesce(sum(case when dir_ind = "INDIRECTO" then 1 else 0 end),0) as indirecto'),
                        DB::RAW('areas.area as name'))
                        ->join('funct_area','reg_assistance.id_function','funct_area.id')
                        ->join('areas','funct_area.id_area','areas.id')
                        // ->where('reg_assistance.id_user_checked','=',$user->id)
                        ->where('reg_assistance.id_aux_treg','=',1)
                        ->whereIn('id_proceso',$proceso)
                        ->whereIn('reg_assistance.id_sede',$sedes)
                        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                        ->groupBy('areas.id')
                        ->where('areas.first_out_group',0)
                        // ->orderBy('areas.area','desc')
                        // ->orderByRaw('FIELD (areas.id, ' . implode(', ', $ids) . ') DESC')
                        // ->orderBy('total','desc')
                        ->get();
                        // dd($datos);
                    }
                }else{
                    if($checked){
                        $datos = Asistencia::select(
                        'color',
                        DB::raw('areas.id as id'),
                        DB::raw('count(*) as total'),
                        DB::raw('coalesce(sum(paga),0) as costos'),
                        DB::raw('coalesce(sum(case when type = "JORNAL" then 1 else 0 end),0) as jornal'),
                        DB::raw('coalesce(sum(case when type = "DESTAJO" then 1 else 0 end),0) as destajo'),
                        DB::raw('coalesce(sum(case when dir_ind = "DIRECTO" then 1 else 0 end),0) as directo'),
                        DB::raw('coalesce(sum(case when dir_ind = "INDIRECTO" then 1 else 0 end),0) as indirecto'),
                        DB::RAW('areas.area as name'))
                        ->join('funct_area','reg_assistance.id_function','funct_area.id')
                        ->join('areas','funct_area.id_area','areas.id')
                        ->whereNull('reg_assistance.deletedAt')
                        ->whereNotNull('reg_assistance.id_user_checked')
                        ->where('reg_assistance.id_aux_treg','=',1)
                        ->where('reg_assistance.id_sede',$sede)
                        ->whereIn('id_proceso',$proceso)
                        ->whereIn('turno',($time===0 ? ["DIA","NOCHE"]:[$time]))
                        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                        ->groupBy('areas.area')
                        ->orderBy('total','desc')
                        ->get();
                    }else{
                        $datos = Asistencia::select(
                        'color',
                        DB::raw('areas.id as id'),
                        DB::raw('count(*) as total'),
                        DB::raw('coalesce(sum(case when checked = 1 then 1 else 0 end),0) as verificados'),
                        DB::raw('coalesce(sum(case when checked = 0 then 1 else 0 end),0) as no_verificados'),
                        DB::raw('coalesce(sum(paga),0) as costos'),
                        DB::raw('coalesce(sum(case when type = "JORNAL" then 1 else 0 end),0) as jornal'),
                        DB::raw('coalesce(sum(case when type = "DESTAJO" then 1 else 0 end),0) as destajo'),
                        DB::raw('coalesce(sum(case when dir_ind = "DIRECTO" then 1 else 0 end),0) as directo'),
                        DB::raw('coalesce(sum(case when dir_ind = "INDIRECTO" then 1 else 0 end),0) as indirecto'),
                        DB::RAW('areas.area as name'))
                        ->join('funct_area','reg_assistance.id_function','funct_area.id')
                        ->join('areas','funct_area.id_area','areas.id')
                        // ->where('reg_assistance.id_user_checked','=',$user->id)
                        ->whereNull('reg_assistance.deletedAt')
                        ->whereIn('id_proceso',$proceso)
                        ->where('reg_assistance.id_aux_treg','=',1)
                        ->where('reg_assistance.id_sede',$sede)
                        ->whereIn('turno',($time===0 ? ["DIA","NOCHE"]:[$time]))
                        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
                        ->groupBy('areas.area')
                        ->orderBy('total','desc')
                        ->get();
                    }
                }
        }
            return view('gerencia.response_areas',compact('datos','datos2','sede','checked','multiplicador'))->render();
        }catch(Exception $ex){
            return "";
        }

    }

    public function loadPieCustomGraphics($start,$end,$sede,$id_user = 0,$checked = false,$proceso= "ALL"){
        $this->config = Auth::user()->getConfig();
        $area = $this->config['areas'];

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        if($proceso === "ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$sede)->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();
        }else{
            $proceso = [$proceso];
        }

        $result = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        $areas = Area::whereIn('id',$area)->orderBy('first_out_group','desc')->get();//todas las areas;
        $total = 0;
        $total_office = 0;
        $contador = 0;
        // $total_c = 0;
        foreach ($areas as $key => $value) {
            # code...
            $asistencia = Asistencia::whereHas('funcion',function($query) use ($value){
                // $query->whereHas('funcion',function($query1) use ($value){
                //     $query1->whereHas('areas',function($query2) use ($value){
                        $query->where('id_area','=',$value->id);
                //     });
                // });
                //just assistance
            })->whereIn('id_sede',$sede)->where('id_aux_treg','=',1)->whereIn('id_proceso',$proceso)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->select(DB::raw('count(*) as asistencia_total'),DB::raw('coalesce(sum(paga),0) as costos'))->first();
            // order by first_out_group , new column in areas table
            // return response()->json($asistencia, 200, $headers);
            // return  $asistencia;


            if($value->first_out_group){
                    if($checked){
                        $result['data'][$contador] = (isset($asistencia->costos))?round($asistencia->costos,2):0;
                        $total += (isset($asistencia->costos))?round($asistencia->costos,2):0;
                    }else{
                        $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                        $result['data'][$contador] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                    }
                    $result['labels'][$contador] = $value->area;
                    $result['backgroundcolor'][$contador] = $value->color;
                    $result['hidden'][$contador] = !in_array($value->id,$this->config['areas']);
                $contador++;
            }else{
                    if($checked){
                        $total += (isset($asistencia->costos))?round($asistencia->costos,2):0;
                        $total_office += (isset($asistencia->costos))?round($asistencia->costos,2):0;
                    }else{
                        $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                        $total_office += (isset($asistencia->asistencia_total))?round($asistencia->asistencia_total,2):0;
                    }
                    $result['data'][$contador] = $total_office;
                    $result['labels'][$contador] = "INDIRECTOS";
                    $result['backgroundcolor'][$contador] = "orange";
                    $result['hidden'][$contador] = false;
                // $contador++;
            }

        }
        $result['success'] = true;
        $result['total'] = ($checked?"S/.":""). round($total,2);

        return response()->json($result, 200, []);
    }

    public function loadSummaryWorker($start,$end,$id_area,$proceso,$sede,$multiplicador = 1){
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        $this->config = Auth::user()->getConfig();
        // $area = $this->config['areas'];

        if($sede==0){
            $sedes = $this->config['sedes'];
        }else{
            $sedes = [$sede];
        }

        if($proceso === "ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$sedes)->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();
        }else{
            $proceso = [$proceso];
        }
        $days = $fecha1->diffInDays($fecha2) + 1;
        // $ids = [16,6,12,40];
        if($days == 1){
            $datos = Asistencia::whereHas('funcion',function($query) use($id_area){
                $query->where('id_area',$id_area);
            })->where('id_aux_treg',1)->whereIn('id_sede',$sedes)->whereIn('id_proceso',$proceso)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->select('id_employe','id_function','type',DB::raw('sum(paga) as costos'),'created_at','deleted_at')
            ->groupBy('id_employe')
            ->orderBy('type','asc')
            ->orderBy('costos','desc')
            // ->orderByRaw('FIELD (id_function, ' . implode(', ', $ids) . ') ASC')
            ->get();
        }else{
            $datos = Asistencia::whereHas('funcion',function($query) use($id_area){
                $query->where('id_area',$id_area);
            })->where('id_aux_treg',1)->whereIn('id_sede',$sedes)->whereIn('id_proceso',$proceso)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->select('id_employe','id_function','type',DB::raw('sum(paga) as costos'))
            ->groupBy('id_employe')
            ->orderBy('type','asc')
            ->orderBy('costos','desc')
            // ->orderByRaw('FIELD (id_function, ' . implode(', ', $ids) . ') ASC')
            ->get();
        }
        // dd($datos);

        return view('gerencia.response_summarywoker',compact('datos','days','multiplicador'));
    }

    public function loadBackOffice($start,$end,$sede,$id_user = 0,$checked = false,$proceso = "ALL"){
        $this->config = Auth::user()->getConfig();
        $area = $this->config['areas'];

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        if($proceso === "ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$sede)->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();
        }else{
            $proceso = [$proceso];
        }

        $result = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        $areas = Area::whereIn('id',$area)->where('first_out_group',0)->orderBy('first_out_group','desc')->get();//todas las areas;
        $total = 0;
        $total_office = 0;
        $contador = 0;
        // $total_c = 0;
        foreach ($areas as $key => $value) {
            # code...
            $asistencia = Asistencia::whereHas('funcion',function($query) use ($value){
                // $query->whereHas('funcion',function($query1) use ($value){
                //     $query1->whereHas('areas',function($query2) use ($value){
                        $query->where('id_area','=',$value->id);
                //     });
                // });
                //just assistance
            })->whereIn('id_sede',$sede)->where('id_aux_treg','=',1)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
            ->select(DB::raw('count(*) as asistencia_total'),DB::raw('coalesce(sum(paga),0) as costos'))->first();
            // order by first_out_group , new column in areas table
            // return response()->json($asistencia, 200, $headers);
            // return  $asistencia;
            if($checked){
                $result['data'][$contador] = (isset($asistencia->costos))?round($asistencia->costos,2):0;
                $total += (isset($asistencia->costos))?round($asistencia->costos,2):0;
            }else{
                $total += (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
                $result['data'][$contador] = (isset($asistencia->asistencia_total))?$asistencia->asistencia_total:0;
            }
            $result['labels'][$contador] = $value->area;
            $result['backgroundcolor'][$contador] = $value->color;
            $result['hidden'][$contador] = !in_array($value->id,$this->config['areas']);
            $contador++;

        }
        $result['success'] = true;
        $result['total'] = ($checked?"S/.":""). round($total,2);

        return response()->json($result, 200, []);
    }

    public function loadAssistance($start,$end,$sede,$proceso){
        $this->config = Auth::user()->getConfig();
        // $area = $this->config['areas'];

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }

        if($proceso === "ALL"){
            // $proceso = \DB::table('areas_sedes')->whereIn('id_sede',$sede)->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
            $proceso = \DB::table('procesos')->select('id')->distinct('id')->get()->pluck('id')->toArray();
        }else{
            $proceso = [$proceso];
        }

        $result = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);

        $result = Asistencia::whereIn('id_sede',$sede)
        ->where('id_aux_treg','=',1)
        ->whereIn('id_proceso',$proceso)
        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
        ->select(DB::raw('coalesce(sum(case when turno = "NOCHE" then 1 else 0 end),0) as noche'),DB::raw('coalesce(sum(case when turno = "DIA" then 1 else 0 end),0) as dia'))->first();
        return response()->json($result,200,[]);
    }

    public function loadDestajo($sede_id,$proceso_id,$start,$end,$costo = 0,$time = 0){
        $response = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        try {
            $response["success"] = false;
            //code...
            $reg_packages = DB::connection('destajo_mysql')->table('package_process')
            ->join('proceso','package_process.proceso_id','proceso.id')
            ->join('sedes','package_process.sede_id','sedes.id')
            ->join('function','package_process.funcion_id','function.id')
            ->join('areas','package_process.area_id','areas.id')
            ->join('package_pro_detail','package_process.id','package_pro_detail.package_process_id')
            ->where('sedes.sede_code',$sede_id)
            ->where('proceso.proceso_code',$proceso_id)// sede_id from package_process
            // ->where(env('DB_DATABASE', '').'.reg_assistance.id_aux_treg',1)
            // ->whereDate('package_process.created_at',\Carbon\Carbon::today()->subDay(4)->toDateString())
            ->where('package_pro_detail.qty','!=',0)
            ->whereBetween(DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")'), [$fecha1->format('Y-m-d'), $fecha2->format('Y-m-d H:i')])
            ->select(
            'package_process.area_id',
            'package_process.funcion_id',
            DB::raw('concat(function.description,".",SUBSTRING(areas.description,1,2)) as description'),
            DB::raw('sum(package_pro_detail.qty) as total'),
            DB::raw('sum(package_pro_detail.qty * package_pro_detail.tarifa) as net_paid'),
            DB::raw('count(distinct package_process.sap_code) as t_workers'),
            'function.background_color'
            // DB::raw('(select count(package_process.sap_code) from package_process inner join sedes on package_process.sede_id = sedes.id inner join proceso on package_process.proceso_id = proceso.id where sedes.sede_code = '.$sede_id.' and proceso.proceso_code = '.$proceso_id.
            // ' and UNIX_TIMESTAMP(DATE_FORMAT(package_process.created_at, "%Y-%m-%d %H:%i")) betweeen '.strtotime($fecha1->format('Y-m-d H:i')).' and '.strtotime($fecha2->format('Y-m-d H:i')).' group by package_process.sap_code ) as t_workers')
            // )
            // DB::raw('count(')
            )
            ->groupBy('package_process.area_id','package_process.funcion_id')
            // ->orderBy('package_process.nro_orden')
            ->get();
            // dd($reg_packages);
            // dd($reg_packages);
            $total = 0;
            $net_paid = 0;
            foreach ($reg_packages as $key => $value) {
                # code...
                $total = $total + $value->t_workers;
                $net_paid = $net_paid + $value->net_paid;
                if($costo == 0){
                    $response["data"][$key] = round($value->t_workers,2);;
                }else{
                    $response["data"][$key] = round($value->net_paid,2);;
                }
                $response["labels"][$key] = $value->description;
                $response["backgroundcolor"][$key] = empty($value->background_color) ? "black":$value->background_color;
                $response["hidden"][$key] = 0;
                $response["success"] = true;
            }
            if($costo == 0){
                $response["total"] = round($total,2);
            }else{
                $response["total"] = "S/.".round($net_paid,2);
            }
            // $response["total"] = $total;
            // $response["net_paid"] = $net_paid;
            $response["reg_packages"] = view('gerencia.response_destajo',compact('reg_packages'))->render();
        } catch (\Throwable $th) {
            //throw $th;
            $response["success"] = false;
            $response["message"] = $th->getMessage();
        }
        return response()->json($response,200,[]);
    }

    public function loadDestajoMembers($sede_id,$proceso_id,$area_id,$funcion_id,$start,$end){
        $response = array();
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);

        $conditional = 'case when '.config('db_conn.DB_DATABASE').'.reg_assistance.id_aux_treg = 1 and '.
        config('db_conn.DB_DATABASE').'.reg_assistance.created_at is not null and '.
        config('db_conn.DB_DATABASE').'.reg_assistance.deletedAt is null ';

        $data = DB::connection('destajo_mysql')->table('package_process')
        ->leftjoin(config('db_conn.DB_DATABASE').'.employes','package_process.sap_code',config('db_conn.DB_DATABASE').'.employes.code')
        ->leftjoin(config('db_conn.DB_DATABASE').'.reg_assistance',config('db_conn.DB_DATABASE').'.employes.id',config('db_conn.DB_DATABASE').".reg_assistance.id_employe")
        ->join('proceso','package_process.proceso_id','proceso.id')
        ->join('sedes','package_process.sede_id','sedes.id')
        ->join('function','package_process.funcion_id','function.id')
        ->join('areas','package_process.area_id','areas.id')
        ->join('package_pro_detail','package_process.id','package_pro_detail.package_process_id')
        ->where('sedes.sede_code',$sede_id)
        ->where('proceso.proceso_code',$proceso_id)// sede_id from package_process
        ->where('package_process.area_id',$area_id)
        ->where('package_process.funcion_id',$funcion_id)
        ->where('package_pro_detail.qty','!=',0)

        ->whereNotNull(config('db_conn.DB_DATABASE').'.reg_assistance.created_at')
        ->whereNull(config('db_conn.DB_DATABASE').'.reg_assistance.deletedAt')
        ->where(DB::raw('DATE_FORMAT('.config('db_conn.DB_DATABASE').'.reg_assistance.created_at, "%Y-%m-%d")'),DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")'))
        ->whereBetween(DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")'), [$fecha1->format('Y-m-d'), $fecha2->format('Y-m-d')])
        ->whereBetween(DB::raw('DATE_FORMAT('.config('db_conn.DB_DATABASE').'.reg_assistance.created_at, "%Y-%m-%d")'), [$fecha1->format('Y-m-d'), $fecha2->format('Y-m-d')])

        ->select(
            DB::raw($conditional.
            'then "ASISTIDO" else "SIN ASISTENCIA" end as assist' ),
            // DB::raw('concat(function.description,".",SUBSTRING(areas.description,1,2)) as description'),
            DB::raw('DATE_FORMAT('.config('db_conn.DB_DATABASE').'.reg_assistance.created_at, "%Y-%m-%d") as created_at'),
            'package_process.sap_code',
            'package_process.fullname',
            'package_process.modulo',
            // DB::raw('sum('.$conditional.'then package_pro_detail.qty else 0 end) as total'),
            DB::raw('sum(package_pro_detail.qty) as total'),
            DB::raw('sum(package_pro_detail.qty * package_pro_detail.tarifa) as t_pago'),
            DB::raw('sum((package_pro_detail.qty * package_pro_detail.tarifa) * 1.13) as t_pago_bruto'),
            DB::raw($conditional.'then '.config('db_conn.DB_DATABASE').'.reg_assistance.created_at else null end as dial_attendance_start' ),
            DB::raw('case when '.config('db_conn.DB_DATABASE').'.reg_assistance.id_aux_treg = 1 and '.
            config('db_conn.DB_DATABASE').'.reg_assistance.deleted_at is not null and '.
            config('db_conn.DB_DATABASE').'.reg_assistance.deletedAt is null then '.config('db_conn.DB_DATABASE').'.reg_assistance.deleted_at else null end as dial_attendance_beat' ),
            DB::raw('MIN(package_pro_detail.created_at) as dial_destajo_start'),
            DB::raw('MAX(package_pro_detail.created_at) as dial_destajo_end'),
            // DB::raw("SEC_TO_TIME(SUM(case when DATE_FORMAT(reg_assistance.created_at, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.deleted_at is not null  and reg_assistance.id_aux_treg = 3 then TIMESTAMPDIFF(MINUTE, reg_assistance.created_at, reg_assistance.deleted_at)*60 else 0 end)) as '".$current->format('Y_m_d')."_DHoras'"),
            DB::raw('SEC_TO_TIME(case when '.config('db_conn.DB_DATABASE').'.reg_assistance.id_aux_treg = 1 and '.
            config('db_conn.DB_DATABASE').'.reg_assistance.created_at is not null and '.
            config('db_conn.DB_DATABASE').'.reg_assistance.deleted_at is not null and '.
            config('db_conn.DB_DATABASE').'.reg_assistance.deletedAt is null then '.
            'TIMESTAMPDIFF(MINUTE, '.config('db_conn.DB_DATABASE').'.reg_assistance.created_at, '.config('db_conn.DB_DATABASE').'.reg_assistance.deleted_at)*60 else 0 end) as biometric_hours'),
            DB::raw('SEC_TO_TIME('.
            'TIMESTAMPDIFF('.
            'MINUTE,MIN(package_pro_detail.created_at),'.
            'MAX(package_pro_detail.created_at)'.
            ') * 60) as real_hours'),
            // DB::raw('count(distinct package_process.sap_code) as t_workers'),
            config('db_conn.DB_DATABASE').'.reg_assistance.c_costo'
        )
        ->groupBy(
            //    ,
            'package_process.sap_code',
            // config('db_conn.DB_DATABASE').'.employes.code',
            // config('db_conn.DB_DATABASE').'.reg_assistance.id_employe',
            DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")'),
            // DB::raw('DATE_FORMAT('.config('db_conn.DB_DATABASE').'.reg_assistance.created_at, "%Y-%m-%d")'),
            // DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")')
        )
        // ->orderBy(DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")'))
        ->orderBy('package_process.modulo')
        ->get();

        $without = DB::connection('destajo_mysql')->table('package_process')
        ->leftjoin(config('db_conn.DB_DATABASE').'.employes','package_process.sap_code',config('db_conn.DB_DATABASE').'.employes.code')
        ->leftjoin(config('db_conn.DB_DATABASE').'.reg_assistance',config('db_conn.DB_DATABASE').'.employes.id',config('db_conn.DB_DATABASE').".reg_assistance.id_employe")
        ->join('proceso','package_process.proceso_id','proceso.id')
        ->join('sedes','package_process.sede_id','sedes.id')
        ->join('function','package_process.funcion_id','function.id')
        ->join('areas','package_process.area_id','areas.id')
        ->join('package_pro_detail','package_process.id','package_pro_detail.package_process_id')
        ->where('sedes.sede_code',$sede_id)
        ->where('proceso.proceso_code',$proceso_id)// sede_id from package_process
        ->where('package_process.area_id',$area_id)
        ->where('package_process.funcion_id',$funcion_id)
        ->where('package_pro_detail.qty','!=',0)
        ->whereBetween(DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")'), [$fecha1->format('Y-m-d'), $fecha2->format('Y-m-d')])
        ->whereNotExists(function($query) use ($fecha1,$fecha2)
        {
            $query->select(DB::raw(1))
                ->from(config('db_conn.DB_DATABASE').'.reg_assistance')
                ->whereColumn(config('db_conn.DB_DATABASE').'.employes.id',config('db_conn.DB_DATABASE').".reg_assistance.id_employe")
                ->whereBetween(DB::raw('DATE_FORMAT('.config('db_conn.DB_DATABASE').'.reg_assistance.created_at, "%Y-%m-%d")'), [$fecha1->format('Y-m-d'), $fecha2->format('Y-m-d')]);
                // ->leftjoin(config('db_conn.DB_DATABASE').'.employes','package_process.sap_code',
                // config('db_conn.DB_DATABASE').'.employes.code')
                // ->leftjoin(config('db_conn.DB_DATABASE').'.reg_assistance',config('db_conn.DB_DATABASE').'.employes.id',config('db_conn.DB_DATABASE').".reg_assistance.id_employe")
                // ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(package_process.created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))]);
        })
        ->select(
            DB::raw($conditional.
            'then "ASISTIDO" else "SIN ASISTENCIA" end as assist' ),
            // DB::raw('concat(function.description,".",SUBSTRING(areas.description,1,2)) as description'),
            DB::raw('DATE_FORMAT('.config('db_conn.DB_DATABASE').'.reg_assistance.created_at, "%Y-%m-%d") as created_at'),
            'package_process.sap_code',
            'package_process.fullname',
            'package_process.modulo',
            // DB::raw('sum('.$conditional.'then package_pro_detail.qty else 0 end) as total'),
            DB::raw('sum(package_pro_detail.qty) as total'),
            DB::raw('sum(package_pro_detail.qty * package_pro_detail.tarifa) as t_pago'),
            DB::raw('sum((package_pro_detail.qty * package_pro_detail.tarifa) * 1.13) as t_pago_bruto'),
            DB::raw('MIN(package_pro_detail.created_at) as dial_destajo_start'),
            DB::raw('MAX(package_pro_detail.created_at) as dial_destajo_end'),
            DB::raw('SEC_TO_TIME('.
            'TIMESTAMPDIFF('.
            'MINUTE,MIN(package_pro_detail.created_at),'.
            'MAX(package_pro_detail.created_at)'.
            ') * 60) as real_hours'),
            config('db_conn.DB_DATABASE').'.reg_assistance.c_costo'
        )
        ->groupBy(
            //    ,
            'package_process.sap_code',
            // config('db_conn.DB_DATABASE').'.employes.code',
            // config('db_conn.DB_DATABASE').'.reg_assistance.id_employe',
            DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")'),
            // DB::raw('DATE_FORMAT('.config('db_conn.DB_DATABASE').'.reg_assistance.created_at, "%Y-%m-%d")'),
            // DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")')
        )
        ->orderBy('package_process.modulo')
        // ->orderBy(DB::raw('DATE_FORMAT(package_process.created_at, "%Y-%m-%d")'))
        ->get();
        // dd($without);
        $response["table"] = view('gerencia.response_destajo_workers',compact('data','without'))->render();
        return response()->json($response,200,[]);
    }

}
