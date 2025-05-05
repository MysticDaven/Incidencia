<?php

namespace App\Services;

use App\Exports\AltoImpactoExport;
use App\Exports\AniosExport;
use App\Exports\DecrementoExport;
use App\Exports\DelitosModalidadExport;
use App\Exports\ExtorsionesExport;
use App\Exports\FeminicidiosExport;
use App\Exports\GraficasExport;
use App\Exports\HomicidiosComparativoExport;
use App\Exports\HomicidiosExport;
use App\Exports\IncidenciaExport;
use App\Exports\IncrementoExport;
use App\Exports\InformativoAcumuladoExport;
use App\Exports\InformativoExport;
use App\Exports\LesionesComparativoExport;
use App\Exports\LesionesExport;
use App\Exports\PrincipalesDelitosExport;
use App\Exports\PrincipalesDelitosMesExport;
use App\Exports\PrivacionExport;
use App\Exports\RoboModalidadExport;
use App\Exports\RoboModalidadMesExport;
use App\Exports\SecuestrosExport;
use App\Exports\TrataExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Files\TemporaryFileFactory;
use ZipArchive;

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

    public function exportPrincipalesDelitos ($rangos, $reporte) {
        $export = new PrincipalesDelitosExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRINCIPALES-DELITOS.xlsm');      
    }

    public function exportAltoImpacto ($rangos, $reporte) {
        $export = new AltoImpactoExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'ALTO.xlsm');
    }

    public function exportPrincipalesDelitosMes ($rangos, $reporte) {
        $export = new PrincipalesDelitosMesExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'DELITOS-MES.xlsm');
    }
    
    public function exportSecuestros ($rangos, $reporte) {
        $export = new SecuestrosExport($rangos, $reporte, $this->temporaryFileFactory);  
        return Excel::download($export, 'SECUESTROS.xlsm');
    }
    
    public function exportExtorsiones ($rangos, $reporte) {
        $export = new ExtorsionesExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'EXTORSIONES.xlsm');
    }

    public function exportHomicidios($rangos, $reporte)
    {
        $path1 = 'exports/HOMICIDIOS.xlsx';
        $path2 = 'exports/HOMICIDIOS-COMPARATIVO.xlsm';
    
        $ok1 = Excel::store(new HomicidiosExport($rangos, $reporte, $this->temporaryFileFactory), $path1);
        $ok2 = Excel::store(new HomicidiosComparativoExport($rangos, $reporte, $this->temporaryFileFactory), $path2);
    
        if (!$ok1 || !$ok2) {
            throw new \Exception("Error al guardar uno o ambos archivos Excel.");
        }
    
        $fullPath1 = storage_path("app/$path1");
        $fullPath2 = storage_path("app/$path2");
    
        if (!file_exists($fullPath1)) {
            throw new \Exception("Archivo no encontrado: $fullPath1");
        }
    
        if (!file_exists($fullPath2)) {
            throw new \Exception("Archivo no encontrado: $fullPath2");
        }
    
        $zipPath = storage_path('app/exports/reporte_homicidios.zip');
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $zip->addFile($fullPath1, '11._Homicidios_KARDEX.xlsx');
            $zip->addFile($fullPath2, '11.1Homicidios_Comparativo_C.xlsm');
            $zip->close();
            unlink($fullPath1);
            unlink($fullPath2);        
        } else {
            throw new \Exception("No se pudo crear el archivo ZIP.");
        }

        return $zipPath;
    }
    

    public function exportPrivacion ($rangos, $reporte) {
        $export = new PrivacionExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRIVACION.xlsm');
    }

    public function exportLesiones ($rangos, $reporte) {
        $path1 = 'exports/LESIONES.xlsx';
        $path2 = 'exports/LESIONES-COMPARATIVO.xlsm';

        $file1 = Excel::store(new LesionesExport($rangos, $reporte, $this->temporaryFileFactory), $path1);
        $file2 = Excel::store(new LesionesComparativoExport($rangos, $reporte, $this->temporaryFileFactory), $path2);

        if (!$file1 || !$file2) {
            throw new \Exception("Error al guardar uno o ambos archivos Excel.");
        }
    
        $fullPath1 = storage_path("app/$path1");
        $fullPath2 = storage_path("app/$path2");
    
        if (!file_exists($fullPath1)) {
            throw new \Exception("Archivo no encontrado: $fullPath1");
        }
    
        if (!file_exists($fullPath2)) {
            throw new \Exception("Archivo no encontrado: $fullPath2");
        }

        $zipPath = storage_path('app/exports/reporte_lesiones.zip');
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $zip->addFile($fullPath1, '10.Lesiones_KARDEX.xlsx');
            $zip->addFile($fullPath2, '10.1Lesiones_Comparativo_C.xlsm');
            $zip->close();
            unlink($fullPath1);
            unlink($fullPath2);        
        } else {
            throw new \Exception("No se pudo crear el archivo ZIP.");
        }

        return $zipPath;
    }
        
    public function exportRoboModalidad ($rangos, $reporte) {
        $export = new RoboModalidadExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'ROBO-MODALIDAD.xlsm');
    }

    public function exportInformativo ($rangos, $reporte) {
        $export = new InformativoExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'INFORMATIVO.xlsm');
    }
    
    public function exportIncremento ($rangos, $reporte) {
        $path1 = 'exports/INCREMENTO.xlsm';
        $path2 = 'exports/DECREMENTO.xlsm';

        $file1 = Excel::store(new IncrementoExport($rangos, $reporte, $this->temporaryFileFactory), $path1);
        $file2 = Excel::store(new DecrementoExport($rangos, $reporte, $this->temporaryFileFactory), $path2);

        if (!$file1 || !$file2) {
            throw new \Exception("Error al guardar uno o ambos archivos Excel.");
        }
    
        $fullPath1 = storage_path("app/$path1");
        $fullPath2 = storage_path("app/$path2");
    
        if (!file_exists($fullPath1)) {
            throw new \Exception("Archivo no encontrado: $fullPath1");
        }
    
        if (!file_exists($fullPath2)) {
            throw new \Exception("Archivo no encontrado: $fullPath2");
        }
    
        $zipPath = storage_path('app/exports/reporte_incremento_decremento.zip');
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $zip->addFile($fullPath1, '6.Incremento_Incidencia.xlsm');
            $zip->addFile($fullPath2, '5.Decremento_Incidencia.xlsm');
            $zip->close();
            unlink($fullPath1);
            unlink($fullPath2);        
        } else {
            throw new \Exception("No se pudo crear el archivo ZIP.");
        }

        return $zipPath;
    }

    public function exportIncidencia ($rangos, $reporte) {
        $export = new IncidenciaExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'INCIDENCIA.xlsm');
    }

    public function exportDelitosModalidad ($rangos, $reporte) {
        $export = new DelitosModalidadExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'DELITOS-MODALIDAD.xlsm');
    }

    public function exportTrata ($rangos, $reporte) {
        $export = new TrataExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'TRATA.xlsm');
    }

    public function exportFeminicidios ($rangos, $reporte) {
        $export = new FeminicidiosExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'FEMINICIDIOS.xlsm');
    }

    public function exportInformativoAcumulado ($rangos, $reporte) {
        $export = new InformativoAcumuladoExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'INFORMATIVO-ACUMULADO.xlsm');
    }

    public function exportRoboModalidadMes ($rangos, $reporte) {
        $export = new RoboModalidadMesExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'ROBO-MODALIDAD-MES.xlsm');
    }

    public function exportGraficas ($rangos, $reporte) {
        $export = new GraficasExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'GRAFICAS.xlsm');
    }
}
