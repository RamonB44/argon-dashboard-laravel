<?php

namespace App\Excel;

use App\Employes;
use App\Procesos;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProceduresImport implements ToModel,WithChunkReading,WithValidation,WithHeadingRow,SkipsOnFailure,SkipsOnError
{
    use Importable,SkipsFailures,SkipsErrors;
    protected int $row;
    protected array $dataSuccessfullyImported;
    //
    public function model(array $row){

        //codigo empleado
        //proceso
        //fecha y hora de registro
        //fecha y hora de cese
        //so , all recorder inserted in table not updated recorder
        $employe = Employes::where('code','=',$row['codigo'])->first();
        $procesos = Procesos::where('name','=',$row['proceso'])->first();
        $fecha_inicio = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_de_inicio']);
        $fecha_cese = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_de_inicio']);
        // dd($fecha);
        if($employe && $procesos){
            ++$this->rows;
            if($employe){$employe->employes_process()->attach($procesos->id,['created_at'=>$fecha_inicio,'deleted_at'=>(isset($row['fecha_de_cese']))?$fecha_cese:null]);}
            $this->dataSuccessfullyImported[] = $employe->employes_process->last()->toArray();
        }

        return $employe;
    }

    public function chunkSize(): int
    {
        return 30;
    }

    public function rules(): array
    {
        $employes = Employes::get('code')->pluck('code')->toArray();
        $procesos = Procesos::get('name')->pluck('name')->toArray();
        // dd($procesos);
        return [
            'codigo' => Rule::in($employes),
            'proceso' => Rule::in($procesos),
            'fecha_de_inicio' => 'required'
             // Above is alias for as it always validates in batches
            // '*.email' => Rule::in(['patrick@maatwebsite.nl']),
        ];
    }

    public function customValidationMessages()
    {
        return [
            'codigo.in' => 'Codigo de empleado no encontrado.',
            'proceso.in' => 'Proceso no registrado.',
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
