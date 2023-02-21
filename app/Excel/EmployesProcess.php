<?php

namespace App\Excel;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;


class EmployesProcess implements FromArray, WithHeadings,ShouldAutoSize,WithTitle
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

    // public function query()
    // {
    //     $data = Employes::query()->whereHas('funcion',function($query){
    //         if($this->area){
    //             $query->where('id_area','=',$this->area);
    //         }
    //         if($this->funcion){
    //             $query->where('id','=',$this->funcion);
    //         }
    //         $query->whereHas('areas',function($query2){
    //             if($this->gerencia){
    //                 $query2->where('id_gerencia','=',$this->gerencia);
    //             }
    //         });
    //     })->where('type',(($this->type)?'=':'!='),$this->type)->where('id_employe_type',(($this->temploye)?'=':'!='),$this->temploye);
    //     return $data;
    // }

    public function title(): string
    {
        return 'Procesos de los empleados';
    }

}
