<?php

namespace App\Services;

use App\Exports\AniosExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Files\TemporaryFileFactory;

class ExportService
{
    protected $temporaryFileFactory;

    public function __construct(TemporaryFileFactory $temporaryFileFactory)
    {
        $this->temporaryFileFactory = $temporaryFileFactory;
    }

    /**
     * Exporta los datos del año proporcionado como un archivo Excel.
     *
     * @param string $anio
     * @return \Illuminate\Http\Response
     */
    public function exportAnio($anio)
    {
        // Crear la instancia de AniosExport
        $export = new AniosExport($anio, $this->temporaryFileFactory);

        // Descargar el archivo Excel
        return Excel::download($export, "Anios_{$anio}.xlsm");
    }
}
