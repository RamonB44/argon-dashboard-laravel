<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Excel\Orders;
use App\Models\Employes;
use App\Excel\Assistance;
use App\Excel\EmployesExport;
use App\Excel\EmployesProcess;
use App\Excel\MultiSheetsExcel;
use App\Excel\MultiSheetsExcelRRHH;
use App\Excel\MultiSheetsRRHH;
use App\Models\Auxiliar\Holidays;
use App\Models\Javas;
use App\Models\Auxiliar\TypeReg;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Area;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel as MaatExcel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Database\Query\JoinClause;

class ExcelController extends Controller
{
    //
    public function __construct(){
        $this->middleware('auth');
    }

    public function getXlsx(){
        $items = array();

        $today = Carbon::today();
        $now = Carbon::now();

        $weekStartDate = $now->startOfWeek()->format('Y-m-d H:i:s');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d H:i:s');
        // return $weekStartDate . "|" . $weekEndDate;
        $fecha1 = Carbon::parse($weekStartDate);
        $fecha2 = Carbon::parse($weekEndDate);
        // return $fecha1->addDays(1) . "|" . $fecha2;
        $diff = $fecha1->diff($fecha2);

        $paint_row = "";
        //row we would be paint in MaatExcel
        $painting = array('E', 'F', 'G', 'H', 'I', 'J', 'K');

        $headings = array();

        $netobyEmploye = 0;

        $employes = Employes::all();

        foreach ($employes as $key => $value) {
            # code...
            $items[$key]["Employe_ID"] = $value->id;
            $items[$key]["Codigo"] = $value->code;
            $items[$key]["Documento"] = $value->doc_num;
            $items[$key]["Nombres"] = $value->fullname;
        }

        foreach ($items as $k => $v) {
            # code...

            for ($i = 0; $i < $diff->days + 1; $i++) {
                # code...
                $fecha1 = Carbon::parse($weekStartDate);
                $current = $fecha1->addDays($i);
                $orden = Order::select(DB::raw('case when sum(net_amount_value) != 0 then sum(net_amount_value) else 0 end as total'))->where('id_employe', '=', $items[$k]["Employe_ID"])->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '=', $current->format('Y-m-d'))->first();
                $items[$k][$current->format('Y-m-d')]  = $orden->total;
                $netobyEmploye = $netobyEmploye + $orden->total;
            }
            $items[$k][]  = $netobyEmploye;
            $netobyEmploye = 0;
        }
        // return $items;

        $headings[0] = "ID";
        $headings[1] = "Codigo";
        $headings[2] = "Documento";
        $headings[3] = "Nombres";

        for ($i = 0; $i < $diff->days + 1; $i++) {
            # code...
            $fecha1 = Carbon::parse($weekStartDate);
            $current = $fecha1->addDays($i);
            if ($current->format('Y-m-d') == $today->format('Y-m-d')) {
                $paint_row = $painting[$i];
            }
            $headings[count($headings)] = $current->format('Y-m-d');
        }
        $headings[] = "TOTAL";
        // return $headings;

        $export = new Orders($items, $headings, $paint_row);
        return  MaatExcel::download($export, 'xlsx_' . $today->format('Y-m-d') . '.xlsx');
    }
    //reporte javas
    public function getXlsxJavas(Request $req){
        $items = array();

        $fecha1 = Carbon::parse($req->start);
        $fecha2 = Carbon::parse($req->end);
        // return $fecha1->addDays(1) . "|" . $fecha2;
        $diff = $fecha1->diff($fecha2);

        $paint_row = "";
        //row we would be paint in MaatExcel
        $painting = array('E', 'F', 'G', 'H', 'I', 'J', 'K');

        $headings = array();

        $netobyEmploye = 0;

        $employes = Employes::all();

        foreach ($employes as $key => $value) {
            # code...
            $items[$key]["Employe_ID"] = $value->id;
            $items[$key]["Codigo"] = $value->code;
            $items[$key]["Documento"] = $value->doc_num;
            $items[$key]["Nombres"] = $value->fullname;
        }

        foreach ($items as $k => $v) {
            # code...

            for ($i = 0; $i < $diff->days + 1; $i++) {
                # code...
                $fecha1 = Carbon::parse($req->start);
                $current = $fecha1->addDays($i);
                $orden = Javas::select(DB::raw('case when count(id_employe) != 0 then count(id_employe) else 0 end as total'))->where('id_employe', '=', $items[$k]["Employe_ID"])->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '=', $current->format('Y-m-d'))->first();
                $items[$k][$current->format('Y-m-d')]  = $orden->total;
                $netobyEmploye = $netobyEmploye + $orden->total;
            }
            $items[$k][]  = $netobyEmploye;
            $netobyEmploye = 0;
        }
        // return $items;

        $headings[0] = "ID";
        $headings[1] = "Codigo";
        $headings[2] = "Documento";
        $headings[3] = "Nombres";

        for ($i = 0; $i < $diff->days + 1; $i++) {
            # code...
            $fecha1 = Carbon::parse($req->start);
            $current = $fecha1->addDays($i);
            if ($current->format('Y-m-d') == $fecha1->format('Y-m-d')) {
                $paint_row = $painting[$i];
            }
            $headings[count($headings)] = $current->format('Y-m-d');
        }
        $headings[] = "TOTAL";
        // return $headings;

        $export = new Orders($items, $headings, $paint_row);
        return  MaatExcel::download($export, 'xlsx_' . $fecha1->format('Y-m-d') . '.xlsx');
    }
    //reporte de asistencia en MaatExcel muti hojas with chart
    public function getXlsxAssistance(Request $req){
        $fecha1 = Carbon::parse($req->start);
        //$fecha2 = Carbon::parse($req->end);

        $assistbyemploye = $this->getAssitancebyEmploye($req->code,$req->start,$req->end,$req->sedes);
        $assistbyarea = $this->getAssistancebyArea($assistbyemploye['assist_data']);
        $directo_inderecto = $this->getAssistancebyEmployeMode($req->start,$req->end,$req->sedes);
        $assistbyareahours = $this->getAssistancebyAreaHours($req->start,$req->end,$req->sedes);
        $assistbytype = $this->getAssitanceByType($req->start,$req->end,$req->sedes);
        // return $assistbyareahours;
        // dd($assistbyareahours);
        $data = array();
        $data['assistance'] = $assistbyemploye['assist_data'];
        $data['assist_area'] = $assistbyarea['assist_data'];
        $data['assist_area_hours'] = $assistbyareahours['assist_data'];
        $data['assist_outin'] = $directo_inderecto['assist_data'];
        $data['assist_type'] = $assistbytype['assist_data'];
        $head = array();
        $head['assistance'] = $assistbyemploye['assist_headings'];
        $head['assist_area'] = $assistbyarea['assist_headings'];
        $head['assist_area_hours'] = $assistbyareahours['assist_headings'];
        $head['assist_outin'] = $directo_inderecto['assist_headings'];
        $head['assist_type'] = $assistbytype['assist_headings'];
        $paint = array();
        $paint['assistance'] = $assistbyemploye['assist_paiting'];
        $paint['assist_area'] = $assistbyarea['assist_paiting'];
        $paint['assist_area_hours'] = $assistbyareahours['assist_paiting'];
        $paint['assist_outin'] = $directo_inderecto['assist_paiting'];
        $paint['assist_type'] = $assistbytype['assist_paiting'];
        $format = array();
        $format['assistance'] = $assistbyemploye['assist_formating'];
        $format['assist_area'] = $assistbyarea['assist_formating'];
        $format['assist_area_hours'] = $assistbyareahours['assist_formating'];
        $format['assist_outin'] = $directo_inderecto['assist_formating'];
        $format['assist_type'] = $assistbytype['assist_paiting'];
        $merged = array();
        $merged['assist_type'] =  $assistbytype['assist_merged'];

        $export = new MultiSheetsExcel($data,$head,$paint,$format,$merged);
        return  MaatExcel::download($export, 'xlsx_' . $fecha1->format('Y-m-d') . '.xlsx');
    }

    /*/private function getLetterArraybyDays($max_count){
        $contado = 0;
        $concatenar = false;
        $letter = null;
        $contado2 = 0;
        $painting = null;
        $contador3 = 0;

        for($i=65; $i<=90; $i++) {
            $letter = chr($i);
            if($concatenar){
                $letter = $painting[$contado2] . chr($i);
            }
            $painting[$contado] = $letter;
            $contado++;
            $max_count--;
            if($contado == 26 || $max_count <= 0){
                $contador3++;
                $concatenar= true;
                if($max_count <= 0){
                    break;
                }else{
                    $i = 64;
                    if($contador3 > 1){
                        $contado2++;
                    }
                }
            }
        }
        return $painting;
    }*/

    private function getAssitancebyEmploye($code,$start,$end,$sede){
        Carbon::setLocale('es');
        $this->config = Auth::user()->getConfig();
        // $items = array();
        $headings = array();

        $sede = $sede == 0 ? $this->config['sedes'] : [$sede];
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);

        $diff = $fecha1->diff($fecha2);

        $headings[0] = "ID";
        $headings[1] = "DIR/IND";
        $headings[2] = "Tipo";
        $headings[3] = "Documento";
        $headings[4] = "Nombres";
        $sub_query = "";

        for($i = 0; $i < $diff->days + 1;$i++){
            //creo las cabeceras para totales y obtener los datos
            $f = Carbon::parse($start);
            $current = $f->addDays($i);
            $headings[count($headings)] = 'CCosto '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Area '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Sede '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Ingreso '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Salida '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Horas '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'D_Horas '.$current->translatedFormat('l/d');

            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then reg_assistance.c_costo else '' end) as '".$current->format('Y_m_d')."_CCOSTO',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then areas.area else '' end) as '".$current->format('Y_m_d')."_AREA',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then sedes.name else '' end) as '".$current->format('Y_m_d')."_SEDE',";

            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then DATE_FORMAT(reg_assistance.created_at, '%Y-%m-%d %H:%i:%s') else '' end) as '".$current->format('Y_m_d')."_INGRESO',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then DATE_FORMAT(reg_assistance.deleted_at, '%Y-%m-%d %H:%i:%s') else '' end) as '".$current->format('Y_m_d')."_SALIDA',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 and reg_assistance.horas_trabajadas != '00:00:00' then reg_assistance.horas_trabajadas else horas_100 end) as '".$current->format('Y_m_d')."_Horas',";

            // $sub_query .= "SEC_TO_TIME(SUM(case when DATE_FORMAT(reg_assistance.created_at, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.deleted_at is not null  and reg_assistance.id_aux_treg = 1 then (case when TIMESTAMPDIFF(MINUTE, reg_assistance.created_at, reg_assistance.deleted_at) > 540 then (TIMESTAMPDIFF(MINUTE, reg_assistance.created_at, reg_assistance.deleted_at) - 60)*60 else TIMESTAMPDIFF(MINUTE, reg_assistance.created_at, reg_assistance.deleted_at)*60 end ) else 0 end) -";
            // $sub_query .= "SUM(case when DATE_FORMAT(reg_assistance.created_at, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.deleted_at is not null  and reg_assistance.id_aux_treg = 3 then TIMESTAMPDIFF(MINUTE, reg_assistance.created_at, reg_assistance.deleted_at)*60 else 0 end)) as '".$current->format('Y_m_d')."_Horas',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."'and reg_assistance.id_aux_treg = 1 then reg_assistance.horas_descontadas else '' end)  as '".$current->format('Y_m_d')."_DHoras'".($diff->days > $i ? ",":"");
            // $query .= "SEC_TO_TIME( ".$current->format('Y_m_d')."_Horas - ".$current->format('Y_m_d')."_DHoras ) as '".$current->format('Y_m_d')."_RHoras'".($diff > $i ? ",":"");
        }

        $headings[count($headings)] = "T.A";
        $headings[count($headings)] = "T.P";

        $items = DB::table('reg_assistance')
        ->join('sedes','reg_assistance.id_sede','sedes.id')
        ->join('employes','reg_assistance.id_employe','employes.id')
        ->join('funct_area','employes.id_function','funct_area.id')
        ->join('areas','funct_area.id_area','areas.id')
        ->where('reg_assistance.id_aux_treg',1)
        ->whereIn('reg_assistance.id_sede',$sede)
        ->whereBetween(DB::raw('DATE_FORMAT(reg_assistance.created_at_search, "%Y-%m-%d %H:%i:%s")'),
        [
            $fecha1->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s'),
            $fecha2->setHours(23)->setMinutes(59)->setSeconds(59)->format('Y-m-d H:i:s')]
            )
        ->select('employes.id as Employe_ID','employes.dir_ind as DIR_IND','employes.type as Tipo','employes.doc_num as Documento','employes.fullname as Nombres'
        ,
        DB::raw($sub_query),
        DB::raw('SUM(case when reg_assistance.id_aux_treg = 1 then 1 else 0 end) as T_ASISTENCIA'),
        DB::raw('SUM(case when reg_assistance.id_aux_treg = 3 then 1 else 0 end) as T_PERMISO')
        )->groupBy('reg_assistance.id_employe')->get()->toArray();


        $all = array();
        $all['assist_data'] = $items;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = "";
        $all['assist_formating'] = [];

        return $all;
    }

    private function getAssistancebyArea($data){
        $all = array();
        //array from employes data
        // $area = null;
        // $x = true;
        // $areas = array();
        $headings = array();
        $headings[] = "Area";
        // $total = 0;
        // $arr = Area::groupBy('area')->get()->pluck('area')->toArray();
        $new = array();
        foreach ($data as $value) {
            # code...
            // dd();
            // dd(is_array($value));
            // dd(array_keys($value));
            (array) $value = (array) $value;
            // dd($value);
            $key_values = array_keys($value);
            $input = preg_quote('_AREA', '~');
            $result = preg_grep('~' . $input . '~', $key_values);
            // dd($result);
            $filtered = array_values(array_filter(
                $value,
                fn ($val, $key) => in_array($key, $result) && (
                    $val != ""
                ),
                ARRAY_FILTER_USE_BOTH
            ));
            array_push($new,$filtered);
        }
        $new = $this->array_icount_values($new,false);
        // $new['TOTAL'] =  array('',$total);
        // dd($new);
        // $areas[0]["TOTAL"] = $total;
        $headings[count($headings)] = "TOTAL";
        // $areas = array_values($areas);
        // return $areas;
        //$letters = $this->getLetterArraybyDays(count($headings));

        // return $headings;

        // exit;
        $all = array();
        $all['assist_data'] = $new;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = null;
        $all['assist_formating'] = [];

        return $all;
    }

    function array_icount_values($arr,$lower=true) {
        $arr2=array();
        $total = 0;
        if(!is_array($arr['0'])){$arr=array($arr);}
            foreach($arr as $v){

                foreach($v as $v2){
                    if($lower==true) {$v2=strtolower($v2);}
                    if(!isset($arr2[$v2])){
                        $arr2[$v2]= [$v2,1];
                    }else{
                        $arr2[$v2][1]++;
                    }
                    $total++;
                }
            }
            $arr2["TOTAL"] = ["",$total];
       return $arr2;
   }

    private function getAssistancebyAreaHours($start,$end,$sede){
        $all = array();
        $sede = $sede == 0 ? $this->config['sedes'] : [$sede];
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);
        $headings = array();
        $headings[] = "Area";
        $headings[] = "Horas";
        $headings[] = "Promedio";

        $datos = array();

        /*$days = $fecha1->diffInDays($fecha2);
        $horas_total = array();//horas
        $personal = array();//horas
        // dd($data);
        for ($i=0; $i < $days + 1; $i++) {
            # code...
            $fecha1 = Carbon::parse($start)->addDays($i);
            foreach ($data as $value) {
                (array) $value = (array) $value;
                # code...
                dd($value);
                $float_val = Carbon::parse("00:00:00");
                // dd($float_val);
                $horas_data = Carbon::parse($value[$fecha1->format('Y_m_d') . "_Horas"])->format('H:i:s');
                // $hora_salida = ($value[$fecha1->format('Y-m-d') . " Hora Salida"])?Carbon::parse($value[$fecha1->format('Y-m-d') . " Hora Salida"]):null;
                $horas = ($horas_data != "00:00:00")? round($float_val->floatDiffInHours($horas_data),2): 0;
                $horas_total[$fecha1->format('Y-m-d'). $value['Area']] = (isset($horas_total[$fecha1->format('Y-m-d'). $value['Area']])?$horas_total[$fecha1->format('Y-m-d'). $value['Area']]:0) + round($horas,2);
                $personal[$fecha1->format('Y-m-d'). $value['Area']] = (isset($personal[$fecha1->format('Y-m-d'). $value['Area']]))?$personal[$fecha1->format('Y-m-d'). $value['Area']]+=1:1;
                if(isset($datos[$value['Area']])){
                    foreach ($datos as $k => $v) {
                        # code...
                        if($k == $value['Area']){
                            $datos[$k][$fecha1->format('Y-m-d').$value['Area']] = $horas_total[$fecha1->format('Y-m-d'). $value['Area']];
                            $datos[$k][$fecha1->format('Y-m-d').$value['Area']." Promedio"] = $horas_total[$fecha1->format('Y-m-d'). $value['Area']] / $personal[$fecha1->format('Y-m-d'). $value['Area']];
                        }
                    }
                }else{
                    $datos[$value['Area']] = array(
                        $value['Area'] => $value['Area'],
                        $fecha1->format('Y-m-d').$value['Area'] => $horas_total[$fecha1->format('Y-m-d'). $value['Area']],
                        $fecha1->format('Y-m-d').$value['Area']." Promedio" => $horas_total[$fecha1->format('Y-m-d'). $value['Area']] / $personal[$fecha1->format('Y-m-d'). $value['Area']]
                    );
                }
            }
            // return $areas;
            $headings[count($headings)] = $fecha1->format('d/m/Y');
            $headings[count($headings)] = "Promedio";
        }*/

        (array) $datos = Asistencia::join('sedes','reg_assistance.id_sede','sedes.id')
        ->join('employes','reg_assistance.id_employe','employes.id')
        ->join('funct_area','employes.id_function','funct_area.id')
        ->join('areas','funct_area.id_area','areas.id')
        ->whereBetween(DB::raw('DATE_FORMAT(reg_assistance.created_at_search, "%Y-%m-%d %H:%i:%s")'),
        [
            $fecha1->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s'),
            $fecha2->setHours(23)->setMinutes(59)->setSeconds(59)->format('Y-m-d H:i:s')]
        )
        ->where('reg_assistance.id_aux_treg',1)
        ->whereIn('reg_assistance.id_sede',$sede)
        ->select(
            'areas.area',
            DB::raw('SEC_TO_TIME( round(SUM( TIME_TO_SEC( reg_assistance.horas_trabajadas )) ) ) as horas'),
            DB::raw('round((SUM( TIME_TO_SEC( reg_assistance.horas_trabajadas ) ) /3600  ) / count(*),2) as promedio')
            )
        ->groupBy('areas.area')
        ->get()->toArray();
        // dd($datos);
        // dd($datos);
        $all = array();
        $all['assist_data'] = $datos;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = null;
        $all['assist_formating'] = [];

        return $all;
    }

    private function getAssistancebyEmployeMode($start,$end,$sede){
        $all = array();
        $sede = $sede == 0 ? $this->config['sedes'] : [$sede];
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);
        $headings = array();
        // $headings[] = "DIA";
        $headings[] = "FECHA";
        $headings[] = "DIR";
        $headings[] = "IND";
        $headings[] = "TOTAL";

        (array) $datos = Asistencia::whereBetween(DB::raw('DATE_FORMAT(reg_assistance.created_at_search, "%Y-%m-%d %H:%i:%s")'),
        [$fecha1->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s'), $fecha2->setHours(23)->setMinutes(59)->setSeconds(59)->format('Y-m-d H:i:s')])
        ->whereIn('reg_assistance.id_aux_treg',[1,3])
        ->whereIn('reg_assistance.id_sede',$sede)
        ->select(/*DB::raw("DATE_FORMAT(reg_assistance.created_at_search, '%D') as DIA"),*/DB::raw("DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') as FECHA"),
        DB::raw('SUM(case when dir_ind = "DIRECTO" then 1 else 0 end) AS DIR'),DB::raw('SUM(case when dir_ind = "INDIRECTO" then 1 else 0 end) as IND'),
        DB::raw('count(dir_ind) as TOTAL'))
        ->get()
        ->toArray();
        // dd($datos);

        $all = array();
        $all['assist_data'] = $datos;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = null;
        $all['assist_formating'] = [];

        return $all;
    }

    private function getAssitanceByType($start,$end,$sede){
        $all = array();
        $sede = $sede == 0 ? $this->config['sedes'] : [$sede];
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);
        //array from employes data
        $areas = array();
        $headings = array();
        $headings[0] = "Area";
        $headings[1] = "Funcion";
        $headings[2] = "Jornal";
        $headings[3] = "Destajo";

        (array) $areas =  Asistencia::join('sedes','reg_assistance.id_sede','sedes.id')
        ->join('employes','reg_assistance.id_employe','employes.id')
        ->join('funct_area','employes.id_function','funct_area.id')
        ->join('areas','funct_area.id_area','areas.id')
        ->whereBetween(DB::raw('DATE_FORMAT(reg_assistance.created_at_search, "%Y-%m-%d %H:%i:%s")'),
        [
            $fecha1->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s'),
            $fecha2->setHours(23)->setMinutes(59)->setSeconds(59)->format('Y-m-d H:i:s')]
        )
        ->where('reg_assistance.id_aux_treg',1)
        ->whereIn('reg_assistance.id_sede',$sede)
        ->select('areas.area','funct_area.description',
        DB::raw('SUM(case when reg_assistance.type = "JORNAL" then 1 else 0 end) as JORNAL'),
        DB::raw('SUM(case when reg_assistance.type = "DESTAJO" then 1 else 0 end) as DESTAJO'),
        DB::raw('count(*) as total'))
        ->groupBy('areas.id','funct_area.description')
        ->get()->toArray();
        // dd($areas);
        $merged = array();
        $headings[count($headings)] = "TOTAL";
        $all = array();
        $all['assist_data'] = $areas;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = null;
        $all['assist_formating'] = [];
        $all['assist_merged'] = $merged;

        return $all;
    }

    public function getXlsxEmploye(Request $req){
        $data = Employes::whereHas('funcion',function($query) use($req){
            // if($req->funcion){
                $query->where('id',(((int)$req->funcion)?'=':'!='),$req->funcion);
            // }
            $query->whereHas('areas',function($query2) use ($req){
                // if($req->area){
                    $query2->where('id',(((int)$req->areas)?'=':'!='),$req->areas);
                // }
            });

        })->where('type',(($req->type)?'=':'!='),$req->type)->where('id_employe_type',(($req->temploye)?'=':'!='),$req->temploye)->get();
        $result = array();
        $headings = array();
        foreach ($data as $key => $value) {
            # code...
            $result[$key]["sede"] = $value->sedes->name;
            $result[$key]["codigo"] = $value->code;
            $result[$key]["dir_ind"] = $value->dir_ind;
            $result[$key]["temploye"] = $value->employes_type->description;
            $result[$key]["doc_num"] = $value->doc_num;
            $result[$key]["nombres"] = $value->fullname;
            $result[$key]["area"] = $value->funcion->areas->area;
            $result[$key]["funcion"] = $value->funcion->description;
            $result[$key]["turno"] = $value->turno;
        }

        $headings[count($headings)] = "Sede";
        $headings[count($headings)] = "Codigo";
        $headings[count($headings)] = "DIR_IND";
        $headings[count($headings)] = "T.Empleado";
        $headings[count($headings)] = "Documento";
        $headings[count($headings)] = "Nombres";
        $headings[count($headings)] = "Area";
        $headings[count($headings)] = "Funcion";
        $headings[count($headings)] = "Turno";
        // return $data;
        return (new EmployesExport($result,$headings))->download('employes.xlsx');
    }

    public function getXlsxEmployeProcess(Request $req){

        $data = Employes::whereHas('funcion',function($query) use($req){
            // if($req->funcion){
                $query->where('id',(((int)$req->funcion)?'=':'!='),$req->funcion);
            // }
            $query->whereHas('areas',function($query2) use ($req){
                // if($req->area){
                    $query2->where('id',(((int)$req->areas)?'=':'!='),$req->areas);
                // }
            });

        })->get();
        $result = array();
        $headings = array();

        foreach ($data as $key => $value) {
            # code...
            if($value->employes_process->count()){
                foreach ($value->employes_process as $k => $v) {
                    # code...
                    $result[$key]["Codigo"] = $value->code;
                    $result[$key]["Nombres"] = $value->fullname;
                    $result[$key]["Area"] = $value->funcion->description;
                    $result[$key]["Funcion"] = $value->funcion->areas->area;
                    $result[$key]["proceso"] =  $v->name;
                    $result[$key]["fecha"] =  Carbon::parse($v->created_at)->format('d/m/Y H:i');
                    // $result[$key] =  $value->;
                }
            }

        }

        $headings[count($headings)] = "Codigo";
        $headings[count($headings)] = "Nombres";
        $headings[count($headings)] = "Area";
        $headings[count($headings)] = "Funcion";
        $headings[count($headings)] = "Proceso";
        $headings[count($headings)] = "Fecha de Inicio";
        // $headings[count($headings)]
        // array_values($result);
        return (new EmployesProcess($result,$headings))->download('employes_process.xlsx');
    }

    public function getXlsxAssistanceRRHH(Request $req){

        $fecha1 = Carbon::parse($req->start);
        $fecha2 = Carbon::parse($req->end);

        $this->config = Auth::user()->getConfig();

        if($req->sedes==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$req->sedes];
        }

        $assistbyemploye = $this->getXlsxRRHH_R($req->code,$req->start,$req->end,$sede);//pesa y es la unica que debe llevar la carga
        $resumeException = $this->getXlsxResumenExcepciones_R($sede,$req->start,$req->end);//reduccion de peso
        $assistRRHH_Horas = $this->getXlsxRRHH_Horas($req->start,$req->end,$sede);//reduccion de peso
        $resumeSAP = $this->getXlsxResumenSAP($req->sedes,$req->start,$req->end);//no pesa
        $resumeExceptionSAP = $this->getXlsxResumenExcepcionesSAP($req->sedes,$req->start,$req->end);//reduccion de peso
        $resumeAbsentismosSAP = $this->getXlsxResumenAbsentismoSAP($req->sedes,$req->start,$req->end);//reduccion de peso

        $data = array();
        $data['assistance'] = $assistbyemploye['assist_data'];
        $data['resumeex'] = $resumeException['assist_data'];
        $data['assistance_horas'] = $assistRRHH_Horas['assist_data'];
        $data["resumesap"] = $resumeSAP["assist_data"];
        $data['resumeexsap'] = $resumeExceptionSAP['assist_data'];
        $data['absentismossap'] = $resumeAbsentismosSAP['assist_data'];
        $head = array();
        $head['assistance'] = $assistbyemploye['assist_headings'];
        $head['resumeex'] = $resumeException['assist_headings'];
        $head['assistance_horas'] = $assistRRHH_Horas['assist_headings'];
        $head['resumesap'] = $resumeSAP['assist_headings'];
        $head['resumeexsap'] = $resumeExceptionSAP['assist_headings'];
        $head['absentismossap'] = $resumeAbsentismosSAP['assist_headings'];
        $paint = array();
        $paint['assistance'] = $assistbyemploye['assist_paiting'];
        $paint['resumeex'] = $resumeException['assist_paiting'];
        $paint['assistance_horas'] = $assistRRHH_Horas['assist_paiting'];
        $paint['resumesap'] = $resumeSAP['assist_paiting'];
        $paint['resumeexsap'] = $resumeExceptionSAP['assist_paiting'];
        $paint['absentismossap'] = $resumeAbsentismosSAP['assist_paiting'];
        $format = array();
        $format['assistance'] = $assistbyemploye['assist_formating'];
        $format['resumeex'] = $resumeException['assist_formating'];
        $format['assistance_horas'] = $assistRRHH_Horas['assist_formating'];
        $format['resumesap'] = $resumeSAP['assist_formating'];
        $format['resumeexsap'] = $resumeExceptionSAP['assist_formating'];
        $format['absentismossap'] = $resumeAbsentismosSAP['assist_formating'];
        $merged = array();
        $export = new MultiSheetsExcelRRHH($data,$head,$paint,$format,$merged);
        return  MaatExcel::download($export, 'xlsx_' . $fecha1->format('Y-m-d') . '.xlsx');
    }

    public function getXlsxRRHH_R($code,$start,$end,$sede){
        Carbon::setLocale('es');
        //funcion reducida y optimizada de la function getXlsxRRHH
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);
        // return $fecha1->addDays(1) . "|" . $fecha2;
        $diff = $fecha1->diffInDays($fecha2);
        //cabeceras para obtener consulta pivot

        $headings[0] = "ID";
        $headings[1] = "Trabajador";
        $headings[2] = "DIR/IND";
        $headings[3] = "Tipo";
        $headings[4] = "Area";
        $headings[5] = "Funcion";
        $headings[6] = "Codigo";
        $headings[7] = "Documento";
        $headings[8] = "Nombres";

        $sub_query = "";

        for($i = 0; $i < $diff + 1;$i++){
            //creo las cabeceras para totales y obtener los datos
            $f = Carbon::parse($start);
            $current = $f->addDays($i);
            $headings[count($headings)] = 'CCosto '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Area '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Sede '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Ingreso '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Salida '.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'Horas '.$current->translatedFormat('l/d');
            //$headings[count($headings)] = 'Horas_100'.$current->translatedFormat('l/d');
            $headings[count($headings)] = 'D_Horas '.$current->translatedFormat('l/d');

            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then reg_assistance.c_costo else '' end) as '".$current->format('Y_m_d')."_CCOSTO',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then areas.area else '' end) as '".$current->format('Y_m_d')."_AREA',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then sedes.name else '' end) as '".$current->format('Y_m_d')."_SEDE',";

            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then DATE_FORMAT(reg_assistance.created_at, '%Y-%m-%d %H:%i:%s') else '' end) as '".$current->format('Y_m_d')."_INGRESO',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 then DATE_FORMAT(reg_assistance.deleted_at, '%Y-%m-%d %H:%i:%s') else '' end) as '".$current->format('Y_m_d')."_SALIDA',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 and reg_assistance.horas_trabajadas != '00:00:00' then reg_assistance.horas_trabajadas else horas_100 end) as '".$current->format('Y_m_d')."_Horas',";

            // $sub_query .= "SEC_TO_TIME(SUM(case when DATE_FORMAT(reg_assistance.created_at, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.deleted_at is not null  and reg_assistance.id_aux_treg = 1 then (case when TIMESTAMPDIFF(MINUTE, reg_assistance.created_at, reg_assistance.deleted_at) > 540 then (TIMESTAMPDIFF(MINUTE, reg_assistance.created_at, reg_assistance.deleted_at) - 60)*60 else TIMESTAMPDIFF(MINUTE, reg_assistance.created_at, reg_assistance.deleted_at)*60 end ) else 0 end) -";
            // $sub_query .= "SUM(case when DATE_FORMAT(reg_assistance.created_at, '%Y-%m-%d') = '".$current->format('Y-m-d')."' and reg_assistance.deleted_at is not null  and reg_assistance.id_aux_treg = 3 then TIMESTAMPDIFF(MINUTE, reg_assistance.created_at, reg_assistance.deleted_at)*60 else 0 end)) as '".$current->format('Y_m_d')."_Horas',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."'and reg_assistance.id_aux_treg = 1 then reg_assistance.horas_descontadas else '' end)  as '".$current->format('Y_m_d')."_DHoras'".($diff > $i ? ",":"");
            // $query .= "SEC_TO_TIME( ".$current->format('Y_m_d')."_Horas - ".$current->format('Y_m_d')."_DHoras ) as '".$current->format('Y_m_d')."_RHoras'".($diff > $i ? ",":"");
        }
        $headings[count($headings)] = "T.A";
        $headings[count($headings)] = "T.P";
        // dd($contact_query_horas);
        // $fecha1 = Carbon::parse($start);

        $sub = DB::table('reg_assistance')
        ->join('sedes','reg_assistance.id_sede','sedes.id')
        ->join('employes','reg_assistance.id_employe','employes.id')
        ->join('employes_type','employes.id_employe_type','employes_type.id')
        ->join('funct_area','employes.id_function','funct_area.id')
        ->join('areas','funct_area.id_area','areas.id')
        ->whereIn('reg_assistance.id_aux_treg',[1,3])
        ->whereIn('reg_assistance.id_sede',$sede)
        ->whereBetween(DB::raw('DATE_FORMAT(reg_assistance.created_at_search, "%Y-%m-%d %H:%i:%s")'),
        [$fecha1->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s'), $fecha2->setHours(23)->setMinutes(59)->setSeconds(59)->format('Y-m-d H:i:s')])
        ->select('employes.id as Employe_ID','employes_type.description as Trabajador','employes.dir_ind as DIR_IND','employes.type as Tipo','areas.area as Area',
        'funct_area.description as Funcion','employes.code as Codigo','employes.doc_num as Documento','employes.fullname as Nombres'
        ,
        DB::raw($sub_query),
        DB::raw('SUM(case when reg_assistance.id_aux_treg = 1 then 1 else 0 end) as T_ASISTENCIA'),
        DB::raw('SUM(case when reg_assistance.id_aux_treg = 3 then 1 else 0 end) as T_PERMISO')
        )->groupBy('reg_assistance.id_employe')->get()->toArray();

        $all = array();
        $all['assist_data'] = $sub;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = "";
        $all['assist_formating'] = [];

        return $all;
    }

    public function getXlsxResumenExcepciones_R($sede,String $start,String $end){
        Carbon::setLocale('es');
        //funcion reducida y optimizada de la function getXlsxRRHH
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);
        // return $fecha1->addDays(1) . "|" . $fecha2;
        $diff = $fecha1->diffInDays($fecha2);
        //cabeceras para obtener consulta pivot

        $headings[0] = "ID";
        $headings[1] = "DIR/IND";
        $headings[2] = "Tipo";
        $headings[3] = "Area";
        $headings[4] = "Funcion";
        $headings[5] = "Codigo";
        $headings[6] = "Documento";
        $headings[7] = "Nombres";

        $sub_query = "";
        //$query = "";
        for($i = 0; $i < $diff + 1;$i++){
            //creo las cabeceras para totales y obtener los datos
            $f = Carbon::parse($start);
            $current = $f->addDays($i);
            $headings[count($headings)] = $current->translatedFormat('l/d');
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$current->format('Y-m-d')."' then aux_type_reg.abr else null end) as '".$current->format('Y_m_d')."'".($diff > $i ? ",":"");
            // $query .= "SEC_TO_TIME( ".$current->format('Y_m_d')."_Horas - ".$current->format('Y_m_d')."_DHoras ) as '".$current->format('Y_m_d')."_RHoras'".($diff > $i ? ",":"");
        }
        $headings[count($headings)] = "T.E";


        $sub = DB::table('reg_assistance')
        ->join('employes','reg_assistance.id_employe','employes.id')
        ->join('funct_area','employes.id_function','funct_area.id')
        ->join('areas','funct_area.id_area','areas.id')
        ->join('aux_type_reg','reg_assistance.id_aux_treg','aux_type_reg.id')
        ->whereNotIn('reg_assistance.id_aux_treg',[1,3])
        ->whereIn('reg_assistance.id_sede',$sede)
        ->whereBetween(DB::raw('DATE_FORMAT(reg_assistance.created_at_search, "%Y-%m-%d %H:%i:%s")'),
        [strtotime($fecha1->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s')), strtotime($fecha2->setHours(23)->setMinutes(59)->setSeconds(59)->format('Y-m-d H:i:s'))])
        ->select('employes.id as Employe_ID','employes.dir_ind as DIR_IND','employes.type as Tipo','areas.area as Area',
        'funct_area.description as Funcion','employes.code as Codigo','employes.doc_num as Documento','employes.fullname as Nombres'
        ,
        DB::raw($sub_query),
        DB::raw('count(*) as T_ABSENTISMO')
        )->groupBy('reg_assistance.id_employe')->get()->toArray();
        // dd($sub);
        $all = array();
        $all['assist_data'] = $sub;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = "";
        $all['assist_formating'] = [];

        return $all;
    }

    public function getXlsxRRHH_Horas($start,$end,$sede){
        Carbon::setLocale('es');
        //$resolution = CarbonInterval::days()->minutes();
        // $items = $employes;

        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);
        // return $fecha1->addDays(1) . "|" . $fecha2;
        //$diff = $fecha1->diff($fecha2);

        $headings = array();
        $formating_rows = array();

        $headings[0] = "ID";
        $headings[1] = "DIR/IND";
        $headings[2] = "Tipo";
        $headings[3] = "Area";
        $headings[4] = "Funcion";
        $headings[5] = "Codigo";
        $headings[6] = "Documento";
        $headings[7] = "Nombres";
        // $headings[8] = "CCosto";
        /*$sub_query = "";
        for ($i = 0; $i < $diff->days + 1; $i++) {
            # code...
            $fecha = Carbon::parse($start);
            $cur = $fecha->addDays($i);
            $headings[count($headings)] = $cur->translatedFormat('l/d');
            //$sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$cur->format('Y-m-d')."' and reg_assistance.id_aux_treg = 1 and reg_assistance.horas_trabajadas != '00:00:00' then reg_assistance.horas_trabajadas else horas_100 end) as '".$cur->format('Y_m_d')."_Horas',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$cur->format('Y-m-d')."' then reg_assistance.horas_trabajadas else '' end) as '".$cur->format('Y_m_d')."_horas',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$cur->format('Y-m-d')."' then reg_assistance.horas_25 else '' end) as '".$cur->format('Y_m_d')."_h_25',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$cur->format('Y-m-d')."' then reg_assistance.horas_35 else '' end) as '".$cur->format('Y_m_d')."_h_35',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$cur->format('Y-m-d')."' then reg_assistance.horas_prima_produccion else '' end) as '".$cur->format('Y_m_d')."_h_prima',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$cur->format('Y-m-d')."' then reg_assistance.horas_100 else '' end) as '".$cur->format('Y_m_d')."_h_100',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$cur->format('Y-m-d')."' then reg_assistance.horas_nocturna else '' end) as '".$cur->format('Y_m_d')."_bono_noct',";
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at_search, '%Y-%m-%d') = '".$cur->format('Y-m-d')."' then reg_assistance.horas_descontadas else '' end) as '".$cur->format('Y_m_d')."desc";
        }*/

        $headings[] = "Horas";
        $headings[] = "H25%";
        $headings[] = "H35%";
        $headings[] = "PRIMA";
        $headings[] = "H100%";
        $headings[] = "BONO NOCT.";
        $headings[] = "H. DESC";

        $items = Asistencia::join('employes','reg_assistance.id_employe','employes.id')
        ->join('funct_area','employes.id_function','funct_area.id')
        ->join('areas','funct_area.id_area','areas.id')
        ->where('reg_assistance.id_aux_treg',1)
        ->whereIn('reg_assistance.id_sede',$sede)
        ->whereBetween(DB::raw('DATE_FORMAT(reg_assistance.created_at, "%Y-%m-%d")'),
        [$fecha1->format('Y-m-d'), $fecha2->format('Y-m-d')])
        ->select('employes.id as Employe_ID','employes.dir_ind as DIR_IND','employes.type as Tipo','areas.area as Area',
        'funct_area.description as Funcion','employes.code as Codigo','employes.doc_num as Documento','employes.fullname as Nombres',
        //DB::raw($sub_query),
        DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( reg_assistance.horas_trabajadas ) ) ) as horas'),
        DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( reg_assistance.horas_25 ) ) ) as horas_25'),
        DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( reg_assistance.horas_35 ) - TIME_TO_SEC( reg_assistance.horas_prima_produccion ) ) ) as horas_35'),
        DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( reg_assistance.horas_prima_produccion ) ) ) as horas_prima_produccion'),
        DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( reg_assistance.horas_100 ) ) ) as horas_100'),
        DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( reg_assistance.horas_nocturna ) ) ) as bono_noct'),
        DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( reg_assistance.horas_descontadas ) ) ) as descuento')
        )->groupBy('reg_assistance.id_employe')->get()->toArray();

        /*foreach ($items as $k => $v) {
            $horas_netas = 0;
            $horas_25 = 0;
            $horas_35 = 0;
            $prima = 0;
            $horas_100 = 0;
            $bono_noct = 0;
            $horas_desc = 0;
            # code...
            $lastRegisters = array();
            $contador_array_delete = 0;

            for ($a = 0; $a < $d; $a++) {

                # code...
                $f = Carbon::parse($start);
                $current = $f->addDays($a);
                $orden = Asistencia::whereIn('id_sede',$sede)->whereIn('id_aux_treg',[1,3])
                ->where('id_employe', '=', $items[$k]["Employe_ID"])
                ->whereDate('created_at', $current->format('Y-m-d'))
                ->orderBy('id_aux_treg','asc')->get();

                $holiday = Holidays::where('day',$current->day)->where('month',$current->month)->count();//feriado
                // if(count($orden)>0){
                //     $contador_array_delete++;
                // }
                $horas = 0;
                $items[$k][$current->format('Y-m-d') . ' Horas']  = 0;
                foreach ($orden as $ky => $valor) {
                    //take the first and last hour of all registers
                    # code...
                    if($valor->id_aux_treg == 3 && !empty($valor->created_at) && !empty($valor->deleted_at)){
                        // $horas++;resta
                        $diff_hours = $valor->created_at
                        ->diffInMinutes(Carbon::parse($valor->deleted_at)) / 60;
                        $horas_netas = $horas_netas - $diff_hours;//descontado del total;
                        $horas -= $diff_hours;
                        $horas_desc = $horas_desc + $diff_hours;

                    }elseif($valor->id_aux_treg == 1 && !empty($valor->created_at) && !empty($valor->deleted_at)){
                        // if($valor->deleted_at!=null){
                        $diff_hours = $valor->created_at
                        ->diffInMinutes(Carbon::parse($valor->deleted_at)) / 60;
                        $h_r = $diff_hours >= 9 ? $diff_hours - 1 : $diff_hours;
                        $horas += $h_r;//horas aumentadas
                        $horas_netas += $h_r;//+ horas
                        $inicio = Carbon::parse($valor->created_at);
                        $fin = Carbon::parse($valor->deleted_at);
                        // print($inicio->format('Y-m-d'));
                        $inicio_limite = Carbon::create($inicio->year, $inicio->month, $inicio->day, 22, 0, 0); //set time to 22:00 : 10PM
                        $final_limite = Carbon::create($fin->year, $fin->month, $fin->day, 6, 0, 0); //set time to 06:00 : 6AM
                        $minutes = 0;
                        if($inicio_limite->between($inicio,$fin,true) || $final_limite->between($inicio,$fin,true)) {
                            // check if 10PM is between start and end date equal in 6AM

                            try {
                                    $minutes = $inicio->diffFiltered($resolution, function (Carbon $date) {
                                        // print($date->hour . ":" . $date->minute. "\n");
                                        return ($date->hour < 6 && $date->hour > 0 ) || ($date->hour > 22 && $date->hour < 24);
                                    }, $fin);
                                    // exit;
                            } catch (\Exception $e) {
                            //   print($inicio->format('Y-m-d H:i:s') . " " . $fin->format('Y-m-d H:i:s'));
                            //   exit;
                                echo $e;
                                exit;
                            }


                            $bono_noct += $minutes/60;
                            // dd($bono_noct);
                        }
                    }
                }
                //calcular y encontrar horas 100
                if($current->dayOfWeek == Carbon::SUNDAY || $holiday > 0){
                    //check if total of assistance is equal to seven in this week
                    $firstdayofweek = $current->copy()->startOfWeek();
                    $enddayofweek = $current->copy()->endOfWeek();
                    $days = Asistencia::where('id_aux_treg',1)
                    ->where('id_employe', '=', $items[$k]["Employe_ID"])
                    ->whereBetween('created_at', [$firstdayofweek,$enddayofweek])->count();//check assistance in current week.
                    if($days >= 6 || $holiday > 0){
                        // $items[$k][$current->format('Y-m-d') . ' Horas']  = round($horas,2);//todas las horas al 100%
                        $horas_100 += $horas;
                    }else{
                        $horas_extras = $horas > 8 ? $horas - 8 : 0;
                        $items[$k][$current->format('Y-m-d') . ' Horas']  = round($horas,2);//horas diarias
                        // $horas_netas += $horas;
                        $horas_25 += $h_25 = $horas_extras > 2 ? 2 : $horas_extras;//acumula
                        $horas_35 += $horas_extras > 2 ? (($horas_extras - $h_25) > 1 ? 1 : $horas_extras - $h_25): 0;
                        $prima += ($horas_extras - $h_25) > 1 ? ($horas_extras - $h_25) - 1:0;//acumula}
                    }
                   //horas diarias
                }else{
                    $horas_extras = $horas > 8 ? $horas - 8 : 0;
                    $items[$k][$current->format('Y-m-d') . ' Horas']  = round($horas,2);//horas diarias
                    // $horas_netas += $horas;
                    $horas_25 += $h_25 = $horas_extras > 2 ? 2 : $horas_extras;//acumula
                    $horas_35 += $horas_extras > 2 ? (($horas_extras - $h_25) > 1 ? 1 : $horas_extras - $h_25): 0;
                    $prima += ($horas_extras - $h_25) > 1 ? ($horas_extras - $h_25) - 1:0;//acumula}
                    // $bono_noct +=
                }
                //end comment
            }

            // $horas_limite = 48;
            $resultado = $horas_netas >= $horas_limite ? $horas_limite : $horas_netas;
            $items[$k]['horas']  = round($resultado,2);
            $items[$k]['h_25'] = round($horas_25,2);
            $items[$k]['h_35'] = round($horas_35,2);
            $items[$k]['h_prima'] = round($prima,2);
            $items[$k]['h_100'] = round($horas_100,2);
            $items[$k]['bono_noct'] = round($bono_noct,2);
            $items[$k]['desc'] = round($horas_desc,2);
            // dd($lastRegisters);
            // if($contador_array_delete==0){
            //     unset($items[$k]);
            // }
        }*/

        $items = array_values($items);

        $all = array();
        $all['assist_data'] = $items;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = "";
        $all['assist_formating'] = $formating_rows;
        // dd($items);
        return $all;
    }

    public function getXlsxRRHH_Horas_R($start,$end,$sede){
                Carbon::setLocale('es');
        //funcion reducida y optimizada de la function getXlsxRRHH
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end);
        // return $fecha1->addDays(1) . "|" . $fecha2;
        $diff = $fecha1->diffInDays($fecha2);
        //cabeceras para obtener consulta pivot

        $headings[0] = "ID";
        $headings[1] = "DIR/IND";
        $headings[2] = "Tipo";
        $headings[3] = "Area";
        $headings[4] = "Funcion";
        $headings[5] = "Codigo";
        $headings[6] = "Documento";
        $headings[7] = "Nombres";

        $sub_query = "";
        $query = "";
        for($i = 0; $i < $diff + 1;$i++){
            //creo las cabeceras para totales y obtener los datos
            $f = Carbon::parse($start);
            $current = $f->addDays($i);
            $headings[count($headings)] = $current->translatedFormat('l/d');
            $sub_query .= "MAX(case when DATE_FORMAT(reg_assistance.created_at, '%Y-%m-%d') = '".$current->format('Y-m-d')."' then aux_type_reg.abr else null end) as '".$current->format('Y_m_d')."'".($diff > $i ? ",":"");
            // $query .= "SEC_TO_TIME( ".$current->format('Y_m_d')."_Horas - ".$current->format('Y_m_d')."_DHoras ) as '".$current->format('Y_m_d')."_RHoras'".($diff > $i ? ",":"");
        }


        dd($sub_query);
    }

    public function getXlsxResumenSAP(int $sede,String $start,String $end){
        // return [$sede,$start,$end];
        Carbon::setLocale('es');
        $this->config = Auth::user()->getConfig();

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }
        // dd($sede);
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59)->setSeconds(59);
        //solo asistencias
        // $diff = $fecha1->diffInDays($fecha2) + 1;
        $data = Asistencia::whereIn('id_sede',$sede)
        ->where('id_aux_treg',1)
        ->whereBetween(DB::raw('DATE_FORMAT(created_at_search, "%Y-%m-%d %H:%i:%s")'), [$fecha1->format('Y-m-d H:i:s'), $fecha2->format('Y-m-d H:i:s')])
        ->orderBy('created_at_search','desc')->get();
        // dd($data);
        $newData = array();
        $contador = 0;
        // return $diff;
        foreach ($data as $value) {
            # code...
            // $newData[] = array_search('[A]',$value);
            // if($key_tosearch){
            //     $newData[] =
            // }
            if($value->created_at){
                $date = Carbon::parse($value->created_at);
                // $morning = Carbon::create($date->year, $date->month, $date->day, 4, 0, 0); //set time to 04:00 : 4AM
                // $evening = Carbon::create($date->year, $date->month, $date->day, 14, 0, 0); //set time to 14:00 : 2PM
                // if($date->between($morning, $evening, true)) {
                $newdate = str_replace(" ","",$date->format('d m Y'));
                // }
                $newData[$contador]["codigo"] = $value->employes->code;
                $newData[$contador]["timr6"] = "";
                $newData[$contador]["begdate"] = $newdate;
                $newData[$contador]["enddate"] = $newdate;
                $newData[$contador]["choic"] = "";
                $newData[$contador]["ldate"] =  $newdate;
                $newData[$contador]["ltime"] =  $date->format('H:i:s');
                $newData[$contador]["satza"] = "P10";//P10 INGRESO, P20 SALIDA
                $newData[$contador]["dallf"] = "+";//POSITIVE DAY, NEGATIVE NIGHT
                $contador++;
                if($value->deleted_at){
                    // $contador++;
                    // dd(Carbon::parse($value->created_at)->diffInDays($value->deleted_at));
                    $end_date = Carbon::parse($value->deleted_at);
                    $newdate = str_replace(" ","",$end_date->format('d m Y'));

                    $newData[$contador]["codigo"] = $value->employes->code;
                    $newData[$contador]["timr6"] = "";
                    $newData[$contador]["begdate"] = $newdate;
                    $newData[$contador]["enddate"] = $newdate;
                    $newData[$contador]["choic"] = "";
                    $newData[$contador]["ldate"] =  $newdate;
                    $newData[$contador]["ltime"] =  $end_date->format('H:i:s');
                    $newData[$contador]["satza"] = "P20";//P10 INGRESO, P20 SALIDA
                    $newData[$contador]["dallf"] = (Carbon::parse($date)->setHours(0)->setMinutes(0)->diffInDays($end_date) > 0 ? "-":"+");//POSITIVE DAY, NEGATIVE NIGHT
                    $contador++;
                }
            }
        }

        usort($newData, function($a, $b) {
            $retval = $a['codigo'] <=> $b['codigo'];
            if ($retval == 0) {
                $retval = $a['ldate'] <=> $b['ldate'];
                if ($retval == 0) {
                    $retval = $a['satza'] <=> $b['satza'];
                }
            }
            return $retval;
        });

        $headings[0] = "PERNR";
        $headings[1] = "TIMR6";
        $headings[2] = "BEGDA";
        $headings[3] = "ENDDA";
        $headings[4] = "CHOIC";
        $headings[5] = "LDATE";
        $headings[6] = "LTIME";
        $headings[7] = "SATZA";
        $headings[8] = "DALLF";

        $all = array();
        $all['assist_data'] = $newData;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = "";
        $all['assist_formating'] = [];

        return $all;
    }

    public function getXlsxResumenExcepcionesSAP(int $sede,String $start,String $end){
        //resumen de faltas , licencia sin goze etc exceptuando asistencias
        //formato similar al de resumenSAP
        Carbon::setLocale('es');
        $this->config = Auth::user()->getConfig();

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }
        // dd($sede);
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        //solo asistencias
        // $diff = $fecha1->diffInDays($fecha2) + 1;
        $data = Asistencia::whereIn('id_sede',$sede)
        ->whereIn('id_aux_treg',[10,4])
        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at_search, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
        ->orderBy('created_at_search','desc')->get();
        // dd($data);
        $newData = array();
        $contador = 0;
        // return $diff;
        foreach ($data as $key => $value) {
            # code...
            // $newData[] = array_search('[A]',$value);
            // if($key_tosearch){
            //     $newData[] =
            // }
            if($value->created_at){
                $date = Carbon::parse($value->created_at);
                $enddate = $value->deleted_at ? Carbon::parse($value->deleted_at) : null;

                $newdate = str_replace(" ",".",$date->format('d m Y'));
                $enddate = empty($enddate) ? "" : str_replace(" ",".",$enddate->format('d m Y'));
                // }
                $newData[$contador]["codigo"] = $value->employes->code;
                $newData[$contador]["timr6"] = "";
                $newData[$contador]["begdate"] = $newdate;
                $newData[$contador]["enddate"] = $enddate;
                $newData[$contador]["timr1"] = "0";
                $newData[$contador]["choic"] = "2001";
                $newData[$contador]["subty"] = $value->aux_type->codigo;
                $newData[$contador]["begda1"] =  $newdate;
                $newData[$contador]["endda1"] =  $enddate;
                // $newData[$contador]["satza"] = "P10";//P10 INGRESO, P20 SALIDA
                // $newData[$contador]["dallf"] = "+";//POSITIVE DAY, NEGATIVE NIGHT
                $contador++;
            }
        }

        usort($newData, function($a, $b) {
            $retval = $a['codigo'] <=> $b['codigo'];
            if ($retval == 0) {
                $retval = $a['begdate'] <=> $b['begdate'];
                // if ($retval == 0) {
                //     $retval = $a['satza'] <=> $b['satza'];
                // }
            }
            return $retval;
        });

        $headings[0] = "PERNR";
        $headings[1] = "TIMR6";
        $headings[2] = "BEGDA";
        $headings[3] = "ENDDA";
        $headings[4] = "TIMR1";
        $headings[5] = "CHOIC";
        $headings[6] = "SUBTY";
        $headings[7] = "BEGDA1";
        $headings[8] = "ENDDA1";

        $all = array();
        $all['assist_data'] = $newData;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = "";
        $all['assist_formating'] = [];

        return $all;
    }

    public function getXlsxResumenAbsentismoSAP(int $sede,String $start,String $end){
        //resumen de descanso, registro de ingreso
                Carbon::setLocale('es');
        $this->config = Auth::user()->getConfig();

        if($sede==0){
            $sede = $this->config['sedes'];
        }else{
            $sede = [$sede];
        }
        // dd($sede);
        $fecha1 = Carbon::parse($start);
        $fecha2 = Carbon::parse($end)->addHours(23)->addMinutes(59);
        //solo asistencias
        // $diff = $fecha1->diffInDays($fecha2) + 1;
        $data = Asistencia::whereIn('id_sede',$sede)
        ->whereIn('id_aux_treg',[1,8])
        ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at_search, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
        ->orderBy('created_at_search','desc')->get();
        // dd($data);
        $newData = array();
        $contador = 0;
        // return $diff;
        // dd($data);
        foreach ($data as $key => $value) {
            # code...
            // dd($value);
            // $newData[] = array_search('[A]',$value);
            // if($key_tosearch){
            //     $newData[] =
            // }
            if($value->created_at){
                $date = Carbon::parse($value->created_at);
                // $enddate = $value->deleted_at ? Carbon::parse($value->deleted_at) : null;
                $hora = $this->roundHalfHoursbyQuarter($date->toTimeString());
                // dd($hora);
                $newdate = str_replace(" ","",$date->format('d m Y'));
                // $enddate = empty($enddate) ? "" : str_replace(" ","",$enddate->format('d m Y'));
                $code = \App\Model\Auxiliar\Suplencia::where('h_goin',$hora)->first();
                // }
                $newData[$contador]["codigo"] = $value->employes->code;
                // $newData[$contador]["timr6"] = "";
                $newData[$contador]["begdate"] = $newdate;
                $newData[$contador]["enddate"] =  $newdate;
                $newData[$contador]["tprog"] = $value->id_aux_treg == 8 ? $value->aux_type->codigo : (empty( $code->code) ? "S/N" :  $code->code);
                $newData[$contador]["hora"] = $value->id_aux_treg == 8 ? "" : $hora;
                // $newData[$contador]["timr1"] = "0";
                // $newData[$contador]["choic"] = "2001";
                // $newData[$contador]["subty"] = $value->aux_type->codigo;
                // $newData[$contador]["begda1"] =  $newdate;
                // $newData[$contador]["endda1"] =  $enddate;
                // $newData[$contador]["satza"] = "P10";//P10 INGRESO, P20 SALIDA
                // $newData[$contador]["dallf"] = "+";//POSITIVE DAY, NEGATIVE NIGHT
                $contador++;
            }
        }

        usort($newData, function($a, $b) {
            $retval = $a['codigo'] <=> $b['codigo'];
            if ($retval == 0) {
                $retval = $a['begdate'] <=> $b['begdate'];
                // if ($retval == 0) {
                //     $retval = $a['satza'] <=> $b['satza'];
                // }
            }
            return $retval;
        });

        $headings[0] = "PERNR";
        $headings[1] = "BEGDA";
        $headings[2] = "ENDDA";
        $headings[3] = "TPROG";
        $headings[4] = "HORA";

        $all = array();
        $all['assist_data'] = $newData;
        $all['assist_headings'] = $headings;
        $all['assist_paiting'] = "";
        $all['assist_formating'] = [];

        return $all;
    }

    private function roundHalfHoursbyQuarter($timestamp){
        //this function get time and round nearest to halft hours
          $hour = date("H", strtotime($timestamp));
          $minute = date("i", strtotime($timestamp));

          if ($minute<15) {
            return date('H:i:s', strtotime("$hour:00") );
          } elseif($minute>=15 and $minute<45){
            return date('H:i:s', strtotime("$hour:30") );
          } elseif($minute>=45) {
            $hour = $hour + 1;
            return date('H:i:s', strtotime("$hour:00") );
          }

    }
}
