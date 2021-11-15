<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyzerResultsExport implements FromArray, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data){
        $this->data = json_decode($data);
    }
    /**
    * @return \Illuminate\Support\Array
    */
    public function array():array
    {
        $collection = [];
        foreach($this->data as $entity => $count){
            $collection[]=[
                $entity,
                $count->count[0],
                $count->count[1],
                $count->htmlCount[0],
                $count->htmlCount[1]
            ];
        }
        return $collection;
    }

    public function headings(): array
    {
        return ["Entity", "Analyzer Mix", "Analyzer Max", "Custom Min", "Custom Max"];
    }

    public function styles(Worksheet $sheet)
{
    return [
       // Style the first row as bold text.
       1    => ['font' => ['bold' => true]],
    ];
}
}
