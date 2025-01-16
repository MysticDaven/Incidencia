<?php

namespace App\Exports;

use App\Models\TuModelo;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReporteExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     // return TuModelo::all();
    // }

    // ----------------------------------------------------------
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // Aquí puedes filtrar los datos según los parámetros recibidos
        // return TuModelo::all();
    }

    public function headings(): array
    {
        return [
            'Columna 1',
            'Columna 2',
            'Columna 3',
            // Agrega más encabezados según sea necesario
        ];
    }
}