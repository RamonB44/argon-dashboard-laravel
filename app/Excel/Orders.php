<?php

namespace App\Excel;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Style\Border;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Orders implements FromArray, WithHeadings,WithEvents,ShouldAutoSize,WithColumnFormatting
{
    //
    protected $invoices;
    protected $headings;
    protected $painting;
    protected static $static_paint;
    protected $formating;

    use Exportable, RegistersEventListeners;
        //

        public function __construct(array $invoices,array $headings,$painting)
        {
            $this->invoices = $invoices;
            $this->headings = $headings;
            $this->painting = $painting;
            self::$static_paint = $painting;
            // $this->formating = $formating;
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
                $cell = self::$static_paint;
                $event->sheet->getDelegate()->getStyle($cell.'1:'.$cell.$event->sheet->getDelegate()->getHighestRow())
                ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffff15');
        }

        public function columnFormats(): array
        {

            return $this->formating;
            // return [
            //     'B' => NumberFormat::FORMAT_DATE_TIME3,
            //     'C' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            // ];
        }
}
