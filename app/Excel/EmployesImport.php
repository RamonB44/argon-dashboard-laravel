<?php

namespace App\Excel;

// use Illuminate\Database\Eloquent\Model;

use App\Area;
use App\Employes;
use App\EmployesType;
use App\Funcion;
use App\Gerencia;
use App\Procesos;
use App\Sedes;
// use Illuminate\Support\Facades\DB;
use DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployesImport implements ToModel,WithChunkReading,SkipsOnError,SkipsOnFailure,WithHeadingRow,WithValidation
{
    use Importable,SkipsFailures,SkipsErrors;
    private int $rows = 0;
    private array $dataSuccessfullyImported = [];

    public function model(array $row)
    {
        //
        // print_r($row[1]);exit;
        $gerencia = Gerencia::updateOrCreate(['description' => $this->sanear_string($row["gerencia"])],['description'=>$this->sanear_string($row["gerencia"])]);
        $area = Area::updateOrCreate(['area' => $this->sanear_string($row["area"]),'id_gerencia' => $gerencia->id],['description'=>$this->sanear_string($row["area"]),'id_gerencia' => $gerencia->id]);
        $funcion = Funcion::updateOrCreate(['description' => $this->sanear_string($row["funcion"]),'id_area' => $area->id],['description' => $this->sanear_string($row["funcion"]),'id_area' => $area->id]);
        $proceso = Procesos::updateOrCreate(['name'=>$this->sanear_string($row["proceso"])],['name'=>$this->sanear_string($row["proceso"])]);
        $sede = Sedes::updateOrCreate(['name' => $this->sanear_string($row["sede"])],['name' => $this->sanear_string($row["sede"])]);
        $employe_type = EmployesType::updateOrCreate(['description' => $this->sanear_string($row["tipo_empleado"])],['description' => $this->sanear_string($row["tipo_empleado"])]);
        // $costo = DB::table('areas_sedes')->where('id_sede',$sede->id)->where('id_area',$area->id)->where('id_proceso',$proceso->id)->first();
        $costo =  DB::select('select * from areas_sedes where id_sede = ? and id_area = ? and id_proceso = ?', array($sede->id,$area->id,$proceso->id));
        // disableinsert
        // dd($costo);
        if(!count($costo)>0){
            // created ->where('c_costo','like','%'.$row['c_costo'].'%')
            // $c_costo = array_push($c_costo,$row['c_costo']);
            $c_costo = [$row['c_costo']];
            DB::table('areas_sedes')->insert(['id_sede'=>$sede->id,'id_area'=>$area->id,'id_proceso'=>$proceso->id,'c_costo'=>json_encode($c_costo)]);
        }else{
            
            $c_costo = json_decode($costo[0]->c_costo);
            $x = true;
            // dd($c_costo
            if(!is_array($c_costo)){
                $c_costo[] = $row["c_costo"];
                $x = false;
            }
            
            $exist = array_search($row['c_costo'],$c_costo);
            
            if($exist === false){
                // dd($exist);
                //if not exist then 
                // dd($row['c_costo']);
                if($x){
                    $c_costo[] = $row['c_costo'];
                }
                // $costo->c_costo =json_encode($c_costo);
                // $costo->save();
                DB::table('areas_sedes')->where('id_sede',$sede->id)->where('id_area',$area->id)->where('id_proceso',$proceso->id)->update(['c_costo'=>json_encode($c_costo)]);
            }
            
        }
        //dd($row);
        //Employes::withTrashed()->find($row["codigo"])->restore();
        $imported = Employes::updateOrCreate([
            'id' => $row["codigo"],
            // 'doc_num' => $row["tipo_empleado"],
        ],[
            'type' => $row["j_d"],
            'code' => $row["codigo"],
            //  => $row["t_emp"]
            'dir_ind' => $row["dir_ind"],
            'id_employe_type' => $employe_type->id,
            'valid' => $row["codigo_validacion"],
            'doc_num' => $row["documento"],
            'fullname' => $row["nombres_apellidos"],
            // 'gerencia' => $row["gerencia"],
            // 'area' => $row["area"],
            'id_function' => $funcion->id,
            'id_proceso' => $proceso->id,
            'id_sede' => $sede->id,
            'turno' => $row["turno"],
            'remuneracion' => $row["remuneracion"],
            'c_costo' => $row['c_costo'],
            'deleted_at' => $row["cese"]//dar de baja
        ]);
        $this->dataSuccessfullyImported[] = $imported->toArray();
        ++$this->rows;
        return $imported;
    }

    public function chunkSize(): int
    {
        return 30;
    }

    public function rules(): array
    {
        // $employes = Employes::get('code')->pluck('code')->toArray();
        $procesos = Procesos::get('name')->pluck('name')->toArray();
        $gerencia = Gerencia::get('description')->pluck('description')->toArray();
        $areas = Area::get('area')->pluck('area')->toArray();
        $sedes = Sedes::get('name')->pluck('name')->toArray();
        $funcion = Funcion::get('description')->pluck('description')->toArray();
        // $
        // // dd($procesos);
        return [
            'proceso' => Rule::in($procesos),
            // 'fecha_de_inicio' => 'required'
            "j_d" => "required|string|regex:/^[A-Z \d\W]+$/",
            "j_d" => Rule::in(['DESTAJO','JORNAL']),
            "codigo" => "required",
            "tipo_empleado"=> "required|string|regex:/^[A-Z \d\W]+$/",
            "tipo_empleado"=> Rule::in(["OBRERO","EMPLEADO"]),
            // 'codigo' => Rule::unique('employes','code'),
            "dir_ind" => "required|string|regex:/^[A-Z \d\W]+$/",
            "dir_ind" => Rule::in(['DIRECTO','INDIRECTO']),
            "codigo_validacion" => "nullable",
            "documento" => "nullable",
            // "documento" => Rule::unique('employes','doc_num'),
            "nombres_apellidos" => "required|string|regex:/^[A-Z \d\W]+$/",
            "gerencia" => "required|string|regex:/^[A-Z \d\W]+$/",
            "area" => "required|string|regex:/^[A-Z \d\W]+$/",
            "funcion" => "required|string|regex:/^[A-Z \d\W]+$/",
            "proceso" => "required|string|regex:/^[A-Z \d\W]+$/",
            "sede"=> "required|string|regex:/^[A-Z \d\W]+$/",
            "turno"=> "required|string|regex:/^[A-Z \d\W]+$/",
            "remuneracion" => 'required|numeric',
            "c_costo" =>  'required|string',
            // "c_costo" => Rule::exists('areas_sedes','')
            "gerencia" => Rule::in($gerencia),
            "area" => Rule::in($areas),
            "funcion" => Rule::in($funcion),//corregir
            // "proceso" => Rule::exists('procesos','name'),
            "sede" => Rule::in($sedes),
            "turno" => Rule::in(["DIA","NOCHE"]),
            "cese" => "nullable"
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.required' => "El campo es requerido",
            '*.string' => "El campo requiere solo caracteres",
            '*.regex' => "Se encontro en el campo , caracteres especiales.",
            '*.in' => "El campo no existe en la base de datos.",
            '*.exists' => "El campo ya existe en la base de datos.",
            'codigo.unique' => "Este codigo de empleado ya existe",
            'documento.digits_between' => "Requiere un minimo de 6 digitos y un maximo de 13",
            'documento.unique' => "Intentaste registrar un nro de documento duplicado",
            // 'documento.max' => "Excediste la cantidad maxima de digitos:11",
            'documento.numeric' => "El campo requiere solo valores numericos",
            // 'c_costo.require' => "El campo"
            // ''
            // 'codigo.in' => 'El codigo de empleado no fue encontrado.',
            // 'proceso.in' => 'El proceso no esta registrado.',
        ];
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getImportedSuccessfully():array{
        return $this->dataSuccessfullyImported;
    }

    function sanear_string($string){

        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("(", ")", "?", "'", "¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "< ", ";", ",", ":",
                "."),
            '',
            $string
        );


        return $string;
    }
}