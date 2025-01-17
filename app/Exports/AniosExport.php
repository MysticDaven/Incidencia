<?php

namespace App\Exports;

use App\Models\Anio;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AniosExport implements WithEvents
{
    protected $anio;
    protected $temporaryFileFactory;

    public function __construct($anio, $temporaryFileFactory)
    {
        $this->anio = $anio;
        $this->temporaryFileFactory = $temporaryFileFactory;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                Log::info("EJECUCIÓN DEL AFTERSHEET");
            },
            BeforeWriting::class => function (BeforeWriting $event) {
                $this->beforeWritting($event);
            },
        ];
    }
    

    public function beforeWritting(BeforeWriting $event)
    {
        Log::info("ENTRANDO EN BeforeWriting");
    
        $templatePath = storage_path('app/templates/plantilla ejemplo.xlsx');
    
        if (!file_exists($templatePath)) {
            throw new \Exception("La plantilla no existe en la ruta especificada: {$templatePath}");
        }
    
        // Cargar la plantilla usando PhpSpreadsheet
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
    
        // Consultar datos
        $anios = Anio::where('anio', $this->anio)->get();
    
        if ($anios->isEmpty()) {
            throw new \Exception("No se encontraron datos para el año {$this->anio}");
        }
    
        // Agregar los datos a la plantilla
        $row = 2; // Comenzamos en la fila 2
        foreach ($anios as $dato) {
            $sheet->setCellValue("A{$row}", $dato->idanio);
            $sheet->setCellValue("B{$row}", $dato->anio);
            $row++;
        }
    
        // Crear un archivo temporal
        $temporaryFile = $this->temporaryFileFactory->makeLocal();
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($temporaryFile->getLocalPath());
    
        // Reabrir el archivo temporal en el evento
        $event->writer->reopen($temporaryFile, \Maatwebsite\Excel\Excel::XLSX);
        $event->writer->getSheetByIndex(0);
    }
}