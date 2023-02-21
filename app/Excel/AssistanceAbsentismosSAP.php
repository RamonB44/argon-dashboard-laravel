<?php

namespace App\Excel;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;

class AssistanceAbsentismosSAP implements FromArray, WithHeadings,WithEvents,ShouldAutoSize,WithColumnFormatting,WithTitle
{
    //
    protected $invoices;
    protected $headings;
    protected $painting;
    protected static $static_paint;
    protected $formating;

    use Exportable, RegistersEventListeners;
        //

    public function __construct(array $invoices,array $headings,string $painting,array $formating)
    {
        $this->invoices = $invoices;
        $this->headings = $headings;
        $this->painting = $painting;
        self::$static_paint = $painting;
        $this->formating = $formating;
    }

    public function array(): array
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public static function afterSheet(AfterSheet $event)
    {
            // $cell = self::$static_paint;
            // $event->sheet->getDelegate()->getStyle($cell.'1:'.$cell.$event->sheet->getDelegate()->getHighestRow())
            // ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffff15');
    }

    public function columnFormats(): array
    {

        return $this->formating;
    }

    public function title(): string
    {
        return 'Infotipo 2003';
    }
}
