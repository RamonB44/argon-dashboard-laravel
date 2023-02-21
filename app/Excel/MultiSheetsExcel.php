<?php

namespace App\Excel;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetsExcel implements FromArray, WithMultipleSheets
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
            new AssistanceByArea($this->sheets['assist_area'],$this->headings['assist_area']),
            new AssistanceByAreaHoras($this->sheets['assist_area_hours'],$this->headings['assist_area_hours']),
            new AssistanceByOutIn($this->sheets['assist_outin'],$this->headings['assist_outin']),
            new AssistanceByType($this->sheets['assist_type'],$this->headings['assist_type'],$this->merged['assist_type']),
        ];

        return $sheets;
    }
}
