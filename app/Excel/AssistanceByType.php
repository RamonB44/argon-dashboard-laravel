<?php

namespace App\Excel;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class AssistanceByType  implements FromArray, WithHeadings,ShouldAutoSize,WithTitle,WithEvents
{
    //
        //
    //
    protected $invoices;
    protected $headings;
    protected $painting;
    protected static $static_paint;
    protected $formating;
    protected static $merged;

    use Exportable, RegistersEventListeners;
        //

        public function __construct(array $invoices,array $headings,array $merged)
        {
            $this->invoices = $invoices;
            $this->headings = $headings;
            self::$merged = $merged;
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
            //merge columns function
            // $mergedt = self::$merged;
            // foreach ($mergedt as $key => $value) {
            //     # code...
            //     $event->sheet->getDelegate()->mergeCells($value);
            // }
        }

        public static function beforeSheet(BeforeSheet $event)
        {
            //
        }

        public function title(): string
        {
            return 'Cantidades GNRL';
        }
}
