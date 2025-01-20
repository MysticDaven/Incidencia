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
                // Log::info("EJECUCIÓN DEL AFTERSHEET");
            },
            BeforeWriting::class => function (BeforeWriting $event) {
                $this->beforeWritting($event);
            },
        ];
    }
    

    public function beforeWritting(BeforeWriting $event)
    {
        // Log::info("ENTRANDO EN BeforeWriting");
    
        $templatePath = storage_path('app/templates/PRINCIPALES_DELITOS_ESTATAL.xlsm');
    
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
        $row = 14; // Comenzamos en la fila 2
        foreach ($anios as $dato) {
            $sheet->setCellValue("C{$row}", $dato->idanio);
            $sheet->setCellValue("D{$row}", $dato->anio);
            $row++;
        }
    
        // Crear un archivo temporal
        $temporaryFile = $this->temporaryFileFactory->makeLocal();
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($temporaryFile->getLocalPath());

    
        // Reabrir el archivo temporal en el evento
        $event->writer->reopen($temporaryFile, \Maatwebsite\Excel\Excel::XLSX);
        $event->writer->getSheetByIndex(0);

        // $outputPath = storage_path('app/exports/anios_2029.xlsm');
        
        // try {
        //     IOFactory::createWriter($spreadsheet, 'Xlsx')->save($outputPath);
        //     // Verifica si la extensión coincide
        //     // if (pathinfo($outputPath, PATHINFO_EXTENSION) !== 'xlsm') {
        //     //     rename($outputPath, storage_path('app/exports/anios_2024.xlsm'));
        //     // }
        //     // Log::info("Archivo generado correctamente en: " . $outputPath);
        // } catch (\Exception $e) {
        //     Log::error("Error al generar el archivo: " . $e->getMessage());
        //     return response()->json([
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }
        
        
    }
}