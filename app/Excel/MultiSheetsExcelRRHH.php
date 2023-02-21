<?php

namespace App\Excel;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetsExcelRRHH implements FromArray, WithMultipleSheets
{
    //
    protected $sheets;
    protected $headings;
    protected $paiting;
    protected $formating;
    protected $merged;

    public function __construct(array $sheets,array $headings, array $paiting, array $formating,array $merged)
    {
        $this->sheets = $sheets;
        $this->headings = $headings;
        $this->paiting = $paiting;
        $this->formating = $formating;
        $this->merged = $merged;
    }

    public function array(): array
    {
        return $this->sheets;
    }

    public function sheets(): array
    {
        $sheets = [
            new Assistance($this->sheets['assistance'],$this->headings['assistance'],$this->paiting['assistance'],$this->formating['assistance']),
            new AssistanceHoras($this->sheets['assistance_horas'],$this->headings['assistance_horas'],$this->paiting['assistance_horas'],$this->formating['assistance_horas']),
            new AssistanceResumeException($this->sheets['resumeex'],$this->headings['resumeex'],$this->paiting['resumeex'],$this->formating['resumeex']),
            new AssistanceResumeSAP($this->sheets['resumesap'],$this->headings['resumesap'],$this->paiting['resumesap'],$this->formating['resumesap']),
            new AssistanceResumeExceptionSAP($this->sheets['resumeexsap'],$this->headings['resumeexsap'],$this->paiting['resumeexsap'],$this->formating['resumeexsap']),
            new AssistanceAbsentismosSAP($this->sheets['absentismossap'],$this->headings['absentismossap'],$this->paiting['absentismossap'],$this->formating['absentismossap']),
        ];

        return $sheets;
    }
}
