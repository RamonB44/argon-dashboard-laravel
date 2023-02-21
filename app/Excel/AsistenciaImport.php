<?php

namespace App\Excel;

// use Illuminate\Database\Eloquent\Model;

// use App\Area;
use App\Employes;
use Carbon\Carbon;
use App\Asistencia;
use App\RegEmployes;
use App\RegUnchecked;
use Illuminate\Support\Collection;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// use DB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AsistenciaImport implements ToCollection,SkipsOnError,SkipsOnFailure,WithHeadingRow,WithValidation,WithBatchInserts,WithChunkReading
{
    use Importable,SkipsFailures,SkipsErrors;
    private int $rows = 0;
    private array $dataSuccessfullyImported = [];
    private array $dataUnSuccesfullyImporterd = [];
    private bool $in_out = true;
    private array $codes = [];
    private array $date = [];
    private $minutes = 60;

    public function __construct($in_out)
    {
        // dd($in_out);
        $this->in_out = $in_out;
    }

    public function collection(Collection $rowss)
    {
        try {
            
            // dd($rowss);
            //code...
            foreach ($rowss as $row) {
                # code...
                // dd($row);
                $time = Carbon::parse($row['fecha'])->timezone('America/Lima');
                $turno = 'S/T';
                $parameter = false;
                $morning = Carbon::create($time->year, $time->month, $time->day, 3, 0, 0); //set time to 03:00 : 3AM // DIA
                $evening = Carbon::create($time->year, $time->month, $time->day, 15, 0, 0); //set time to 15:00 : 3PM // DIA
    
                $night_r1 = Carbon::create($time->year, $time->month, $time->day, 15, 0, 1); // set time 15:00:01 : 3PM
                $night_r2 = Carbon::create($time->year, $time->month, $time->day, 23, 0, 0); // set time 23:00:00 : 11PM
                $this->codes[] = $row["codigo"];
                $this->date[$time->toDateString()] = $time->toDateString();   
                $employe = Employes::where('id',$row["codigo"])->first();//datos del trabajador
                // dd();

                if($this->in_out == true){
                    
                        if($time->between($morning, $evening, true)) {
                            //current time is between morning and evening
                            $turno = "DIA";
                            $parameter = true;
                        }
    
                        if($time->between($night_r1, $night_r2, true)){
                            $turno = "NOCHE";
                            $parameter = true;
                            //current time is earlier than morning or later than evening
                        }

                        
                        if($parameter && $employe){
                            
                            $javas_pas = Asistencia::where('id_employe', $row["codigo"])
                            ->whereNotIn('id_aux_treg',[10,3,7])
                            ->whereDate('created_at_search','<=',$time->toDateString())
                            ->whereNull('deletedAt')
                            ->orderBy('created_at_search','desc')
                            ->orderBy('created_at','desc')
                            ->first();
                            // dd($javas_pas);
                            if(!$javas_pas){
                                $this->asistencia($employe,$time,$turno);
                            }else{
                                if($javas_pas->created_at && $javas_pas->deleted_at){
                                    if(Carbon::parse($javas_pas->created_at)->lt($time->toDateString(),false) && Carbon::parse($javas_pas->deleted_at)->lt($time->toDateString())){
                                        $this->asistencia($employe,$time,$turno);
                                    }
                                }elseif($javas_pas->created_at && is_null($javas_pas->deleted_at)){
                                    //fecha de inicio menor o igual a la fecha de registro
                                    //dd($register);
                                    if(Carbon::parse($javas_pas->created_at)->lt($time->toDateString(),false)){
                                        $this->asistencia($employe,$time,$turno);
                                    }
            
                                }elseif(is_null($javas_pas->created_at) && $javas_pas->deleted_at){
                                    //fecha de salida es menor o igual a la fecha de registro
                                    if(Carbon::parse($javas_pas->deleted_at)->lt($time,false)) {
                                        $this->asistencia($employe,$time,$turno);
                                    }
                                }
                            }
                        }
                        // return $employe;
                }else{
                    $resolution = CarbonInterval::minutes();
                    $time = Carbon::parse($row['fecha'])->timezone('America/Lima');
                        //busqueda para saber si el personal esta fuera del rango de cese
                        if($employe){
                            $javas_pas = Asistencia::where('id_employe', $employe->id)
                            ->whereNotIn('id_aux_treg',[10,3,7])
                            // ->where('hasObs',1)
                            ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:%s")'),'<',Carbon::parse($row['fecha'])->toDateTimeString())
                            ->where(DB::raw('TIMESTAMPDIFF(HOUR, created_at, "'.Carbon::parse($row['fecha'])->toDateTimeString().'")'),'<',23)
                            ->where(DB::raw('TIMESTAMPDIFF(HOUR, created_at, "'.Carbon::parse($row['fecha'])->toDateTimeString().'")'),'>',1)
                            ->whereDate('created_at_search','<=',$time->toDateString())
                            // ->whereBetween(DB::raw('DATE_FORMAT(created_at_search, "%Y-%m-%d %H:%i:%s")'),
                            // [$time->copy()->setHours(0)->setMinutes(0)->setSeconds(0)->subDay()->toDateTimeString(),//ayer a las 2020-09-08 00:00:00
                            // $time->copy()->setHours(23)->setMinutes(59)->setSeconds(59)->toDateTimeString()])//hoy a las 2020-09-08 23:59:59
                            //->whereDate('created_at_search',Carbon::parse($row['fecha'])->toDateString())
                            ->whereNull('deletedAt')
                            ->select('*',DB::raw( "'".Carbon::parse($row['fecha'])->toDateTimeString() . "' as fecha_subir"))
                            ->orderBy('created_at_search','desc')
                            ->orderBy('created_at','desc')->first();
    
                                if(!$javas_pas){
                                    // si no encuentra registra el ingreso
                                    $this->setSalida($time,$employe);
                                    // $message[] = true;
                                    $this->dataSuccessfullyImported[] = "Salida registrada " . strtolower($employe->fullname);
                                }else{
                                    if($javas_pas->id_aux_treg == 1 && $javas_pas->created_at && is_null($javas_pas->deleted_at)){
                                        $fecha1 = Carbon::parse($javas_pas->created_at);// hora de ingreso 18:00:00
                                        $fecha2 = Carbon::parse($row["fecha"]);// hora de salida 02:00:00
            
                                        $mins = $fecha1->diffInMinutes($fecha2, true);
            
                                        $horas = $mins/60;
                                        // check ingreso is greater than salida
                                        if($fecha2->gt($fecha1)){
                                            if($horas >= 23){
                                                $this->setSalida($time,$employe);
                                            }elseif($horas <= 1){
                                                //do nothing
                                            }else{
                                                //complete cooldown time then register a beat
                                                // $message["success"] = false;
                                                if($javas_pas->created_at && !$javas_pas->deleted_at && $fecha2->gt($fecha1,false)){
                                                    //registra salida en el registro
                                                    $days = Asistencia::where('id_aux_treg',1)->whereNull('deletedAt')->where('id_employe',$javas_pas->id_employe)->whereBetween('created_at', [$fecha1->startOfWeek(), $fecha1->endOfWeek()])->count();
            
                                                    if($javas_pas->created_at->dayOfWeek == Carbon::SUNDAY && $days >=6){
                                                        //añadir validacion de dias asistidos en la semana == 6
                                                        $pago_real = round((($employe->remuneracion / 8)* 2) * $horas,2);
                                                        $javas_pas->paga_100 = round($pago_real *2,2);
                                                        $javas_pas->horas_100 = sprintf('%02d:%02d:00', $horas, fmod($horas, 1) * 60);
                                                    }else{
                                                        $horas_nocturna = 0;
                                                        $horas = $horas > 8 ? $horas - 1 : $horas;
            
                                                        $javas_pas->hasDinner = $horas > 8 ? 1 : 0;//($req->hasDinner == "1" ? 1 : 0);
            
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
            
                                                    // $javas_pas->created_at_search = $fecha1->toDateString();
                                                    // $javas_pas->created_at = $fecha1->toDateTimeString();
                                                    $javas_pas->deleted_at = $fecha2->toDateTimeString();
                                                    $javas_pas->hasObs = 0;
                                                    $javas_pas->synchronized_users = "[]";
                                                    $javas_pas->save();
                                                    // $message['success'] = true;
                                                    //$this->dataSuccessfullyImported[] = " Salida registrada " .strtolower($employe->fullname);
                                                    //delete Registro
                                                //delete record in current day
                                                // if(Carbon::today()->isSameDay($time->toDateString())){
                                                    $reg_un = DB::table('reg_unchecked')->where('reg_unchecked.reg_code',$row['codigo'])->whereDate('reg_unchecked.created_at',$time->toDateString())->first();
                                                    $reg_em = DB::table('reg_employes')->where('reg_employes.code',$row['codigo'])->whereDate('reg_employes.created_at',$time->toDateString())->first();
            
                                                    if($reg_un || $reg_em){
                                                        $params = ($reg_un ? [$reg_un->id_user,$reg_un->created_at,1] : ($reg_em ? [$reg_em->id_user,$reg_em->created_at,1] : [null,null,0]) );
                                                        $javas_pas->id_user_checked = $params[0];
                                                        $javas_pas->checked_at = $params[1];
                                                        $javas_pas->checked = $params[2];
                                                        $javas_pas->synchronized_users = "[]";
                                                        $javas_pas->save();
                                                    }
            
                                                    DB::table('reg_unchecked')->where('reg_unchecked.reg_code',$row['codigo'])->whereDate('reg_unchecked.created_at',$time->toDateString())->delete();
                                                    DB::table('reg_employes')->where('reg_employes.code',$row['codigo'])->whereDate('reg_employes.created_at',$time->toDateString())->delete();
                                                    // }
                                                    //delete FALTA
                                                    // Asistencia::where('id_employe',$employe->id)->where('id_aux_treg',10)->whereDate('created_at',$time->toDateString())->forceDelete();
                                                }elseif($javas_pas->deleted_at){
                                                    //76
                                                    //delete record in current day
                                                    // if(Carbon::today()->isSameDay($time->toDateString())){
                                                        $reg_un = DB::table('reg_unchecked')->where('reg_unchecked.reg_code',$row['codigo'])->whereDate('reg_unchecked.created_at',$time->toDateString())->first();
                                                        $reg_em = DB::table('reg_employes')->where('reg_employes.code',$row['codigo'])->whereDate('reg_employes.created_at',$time->toDateString())->first();
                        
                                                        if($reg_un || $reg_em){
                                                            $params = ($reg_un ? [$reg_un->id_user,$reg_un->created_at,1] : ($reg_em ? [$reg_em->id_user,$reg_em->created_at,1] : [null,null,0]) );
                                                            $javas_pas->id_user_checked = $params[0];
                                                            $javas_pas->checked_at = $params[1];
                                                            $javas_pas->checked = $params[2];
                                                            $javas_pas->synchronized_users = "[]";
                                                            $javas_pas->save();
                                                        }
                        
                                                        DB::table('reg_unchecked')->where('reg_unchecked.reg_code',$row['codigo'])->whereDate('reg_unchecked.created_at',$time->toDateString())->delete();
                                                        DB::table('reg_employes')->where('reg_employes.code',$row['codigo'])->whereDate('reg_employes.created_at',$time->toDateString())->delete();
                                                        // }
                                                        //$this->dataUnSuccesfullyImporterd[] = "Ya registraste una " . $javas_pas->aux_type->description . ($javas_pas->id_aux_treg ==1 && $javas_pas->created_at == null ? " solo salida":"");
                                                }
            
                                            }
                                        }
                                    elseif($javas_pas->id_aux_treg != 1){
                                        //cualquier tipo de registro
                                        if(Carbon::parse($javas_pas->deleted_at)->lte($time)) {
                                            //verifica hasta que dia y hora existe el registro si es menor al que se ingresara entonces registra solo la salida del dia 
                                            $this->setSalida($time,$employe);
                                        }
                                    }
                                }

                            }
                        }
    
                    //}
                }
            }
            
        } catch (\Throwable $th) {
            // throw $th;
            //return null;   
            if($th->getCode() != 23000) //Violation integrity
            {
                DB::rollBack();
            }
        }
        
        // return $employe;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function rules(): array
    {
        $employes = Employes::get('code')->pluck('code')->toArray();
        return [
            "codigo" => "required",
            "codigo" => Rule::In($employes),
            "fecha" => "required"
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.required' => "El campo es requerido",
            'codigo.in' => "El Codigo no Existe",
        ];
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getImportedSuccessfully():array{
        return $this->dataSuccessfullyImported;
    }

    public function getImportedunSuccessfully():array{
        return $this->dataUnSuccesfullyImporterd;
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
            $asistencia->uniqueReg = $today->copy()->addDay()->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d').".8.".$value->turno.".S/T";
            $asistencia->created_at = $today->copy()->addDay()->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s');
            $asistencia->deleted_at = $today->copy()->addDay()->setHours(23)->setMinutes(59)->setSeconds(59)->format('Y-m-d H:i:s');
            $asistencia->save();
        }
    }

    private function setSalida(Carbon $time,Employes $employe){
        Asistencia::where('id_employe',$employe->id)->where('id_aux_treg',1)->where('turno',"S/T")->whereDate('deleted_at',$time->toDateString())->forceDelete();
        $id_aux_treg = 1;
        $javas_pas = new Asistencia();
        $javas_pas->id_employe = $employe->id;
        $javas_pas->id_aux_treg = $id_aux_treg;
        $javas_pas->id_function = $employe->id_function;
        $javas_pas->id_employe_type = $employe->id_employe_type;
        $javas_pas->dir_ind = $employe->dir_ind;
        $javas_pas->type = $employe->type;
        $javas_pas->c_costo = $employe->c_costo;
        $javas_pas->turno = "S/T";
        $javas_pas->synchronized_users = "[]";
        $javas_pas->created_at_search = $time->toDateTimeString();
        $javas_pas->deleted_at = $time->toDateTimeString();
        $javas_pas->created_at = null;
        $javas_pas->id_proceso = $employe->id_proceso;
        $javas_pas->uniqueReg = $time->toDateString().".".$id_aux_treg.".".$employe->id.".S/T";
        $javas_pas->id_sede = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0]; //register asistance with first sede register from user
        $javas_pas->save();
        Asistencia::where('id_employe',$employe->id)->where('id_aux_treg',10)->whereDate('created_at',$time->toDateString())->forceDelete();
        if($time->dayOfWeek == Carbon::SUNDAY){
            Asistencia::where('id_employe',$employe->id)->where('id_aux_treg',8)->whereDate('created_at',$time->toDateString())->forceDelete();
        }

    }

    private function asistencia($employe,$time,$turno){
        // dd($employe);
        $id_aux_treg = 1;
        $parametros["id_employe"] = $employe->id;
        $parametros["id_aux_treg"] = $id_aux_treg;
        $parametros["id_function"] = $employe->id_function;
        $parametros["id_employe_type"] = $employe->id_employe_type;
        $parametros["dir_ind"] = $employe->dir_ind;
        $parametros["type"] = $employe->type;
        $parametros["basico"] = $employe->remuneracion;
        $parametros["id_proceso"] = $employe->id_proceso;
        $parametros["c_costo"] = $employe->c_costo;
        $parametros["turno"] = $turno;
        $parametros["synchronized_users"] = "[]";
        $parametros["uniqueReg"] = $time->toDateString().".".$id_aux_treg.".".$employe->id.".".$turno;
        $parametros["created_at"] = $time->toDateTimeString();
        $parametros["created_at_search"] = $time->toDateString();
        $parametros["id_sede"] = json_decode(Auth::user()->user_group->first()->pivot->sedes)[0]; //register asistance with first sede register from user
        
        if($time->dayOfWeek == Carbon::SATURDAY){
            $this->setDescanso($time,$employe);// coloca descanso para el domingo
        }elseif($time->dayOfWeek == Carbon::SUNDAY){
            //elimina descanso del domingo , si asiste el dia domingo
            Asistencia::where('id_employe',$employe->id)->where('id_aux_treg',8)->whereDate('created_at_search',$time->toDateString())->forceDelete();
        }

        $reg_un = RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$time->toDateString())->first();
        $reg_em = RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$time->toDateString())->first();

        if($reg_un || $reg_em){
            $params = ($reg_un ? [$reg_un->id_user,$reg_un->created_at,1] : ($reg_em ? [$reg_em->id_user,$reg_em->created_at,1] : [null,null,0]) );
            $parametros["id_user_checked"] = $params[0];
            $parametros["checked_at"] = $params[1];
            $parametros["checked"] = $params[2];
            $parametros["synchronized_users"] = "[]";
            // $javas_pas->save();
        }

        RegUnchecked::where('reg_unchecked.reg_code',$employe->id)->whereDate('reg_unchecked.created_at',$time->toDateString())->forceDelete();
        RegEmployes::where('reg_employes.code',$employe->id)->whereDate('reg_employes.created_at',$time->toDateString())->forceDelete();
        //delete FALTA
        Asistencia::where('id_employe',$employe->id)->where('id_aux_treg',10)->whereDate('created_at_search',$time->toDateString())->forceDelete();
        // $message['success'] = true;
        $this->dataSuccessfullyImported[] = "Asistencia " . strtolower($employe->fullname);
        DB::beginTransaction();
        DB::table('reg_assistance')->insert($parametros);
        DB::commit();
        // dd("ok");
    }
    // @parameter Array $row
    public function getFullData():array{
        $fullData["codigos"] = $this->codes;
        $fullData["fechas"] = $this->date;
        return $fullData;
    }

}
