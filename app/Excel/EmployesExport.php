<?php

namespace App\Excel;

use App\Employes;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

// use Maatwebsite\Excel\Concerns\FromArray;
// use Maatwebsite\Excel\Concerns\FromQuery;

class EmployesExport implements FromArray, WithHeadings,ShouldAutoSize,WithTitle
{
    //
    use Exportable;
    private $invoices;
    private $headings;


    public function __construct(array $invoices,array $headings)
    {
        $this->invoices = $invoices;
        $this->headings = $headings;
        // $this->gerencia = $gerencia;
    }

    public function array(): array
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return 'Lista de Empleados';
    }

}
