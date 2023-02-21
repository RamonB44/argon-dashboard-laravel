<?php

namespace App\Excel;

// use Illuminate\Database\Eloquent\Model;

use App\Employes;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
// use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployesToggle implements ToModel,WithChunkReading,WithValidation,WithHeadingRow,SkipsOnFailure,SkipsOnError
{
    //
    use Importable,SkipsFailures,SkipsErrors;
    protected int $rows = 0;
    protected array $dataSuccessfullyImported = [];
    private $columna = null;
    private $tabla_validacion = null;
    private $columna_validacion = null;

    public function __construct(String $columna,String $tabla_validacion,String $columna_validacion)
    {
        $this->columna = $columna;//donde se insetara
        $this->tabla_validacion = $tabla_validacion;
        $this->columna_validacion = $columna_validacion;
    }
    //
    public function model(array $row){
        // dd($this->columna);
        switch ($this->columna) {
            case 'id_function':
            case 'id_employe_type':
            case 'id_proceso':
                # code...
                $valor = DB::table($this->tabla_validacion)->where($this->columna_validacion,$row[$this->columna])->select('id')->first();
                $row[$this->columna] = $valor->id;
                // dd("NO");
                break;
            default:
                
                # code...
                break;
        }

        Employes::where('code',$row["codigo"])->update([$this->columna => $row[$this->columna]]);
        $record = Employes::where('code',$row["codigo"])->first();
        // dd($row);
        ++$this->rows;
        $this->dataSuccessfullyImported[] = $record;
        return $record;
    }

    public function chunkSize(): int
    {
        return 30;
    }

    public function rules(): array
    {
        $employes = Employes::get('code')->pluck('code')->toArray();
        $validacion = DB::table($this->tabla_validacion)->groupBy($this->columna_validacion)->get($this->columna_validacion)->pluck($this->columna_validacion)->toArray();
        // dd($validacion);
        return [
            'codigo' => [Rule::in($employes),"required"],
            $this->columna => [Rule::in($validacion),"required"],
            // 'fecha_de_inicio' => 'required'
             // Above is alias for as it always validates in batches
            // '*.email' => Rule::in(['patrick@maatwebsite.nl']),
        ];
    }

    public function customValidationMessages()
    {
        return [
            'codigo.required' => 'Codigo requerido',
            'codigo.in' => 'Codigo de empleado no encontrado.',
            $this->columna.'.required' => $this->columna. " requerido",
            $this->columna.'.in' => $this->columna.' no registrado.',
        ];
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getImportedSuccessfully():array{
        return $this->dataSuccessfullyImported;
    }
}
