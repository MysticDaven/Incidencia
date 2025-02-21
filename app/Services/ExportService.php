<?php

namespace App\Services;

use App\Exports\AltoImpactoExport;
use App\Exports\AniosExport;
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
use App\Exports\LesionesExport;
use App\Exports\PrincipalesDelitosExport;
use App\Exports\PrincipalesDelitosMesExport;
use App\Exports\PrivacionExport;
use App\Exports\RoboModalidadExport;
use App\Exports\RoboModalidadMesExport;
use App\Exports\SecuestrosExport;
use App\Exports\TrataExport;
use Illuminate\Support\Facades\Log;
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

    public function exportPrincipalesDelitos ($rangos, $reporte) {
        $export = new PrincipalesDelitosExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA.xlsm');

        
        // return response()->json([
        //     'rangos' => $rangos,
        //     'reporte' => $reporte,
        //     'up' => 'down'
        // ]);         
    }

    public function exportAltoImpacto ($rangos, $reporte) {
        $export = new AltoImpactoExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-ALTO.xlsm');
    }

    public function exportPrincipalesDelitosMes ($rangos, $reporte) {
        $export = new PrincipalesDelitosMesExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-DELITOS-MES.xlsm');
    }

    public function exportSecuestros ($rangos, $reporte) {
        $export = new SecuestrosExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-SECUESTROS.xlsm');
    }

    public function exportExtorsiones ($rangos, $reporte) {
        $export = new ExtorsionesExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-EXTORSIONES.xlsm');
    }

    public function exportHomicidios ($rangos, $reporte) {
        $export = new HomicidiosExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-HOMICIDIOS.xlsx');
    }

    public function exportHomicidiosComparativo ($rangos, $reporte) {
        $export = new HomicidiosComparativoExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-HOMICIDIOS-COMPARATIVO.xlsm');
    }

    public function exportPrivacion ($rangos, $reporte) {
        $export = new PrivacionExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-PRIVACION.xlsm');
    }

    public function exportLesiones ($rangos, $reporte) {
        $export = new LesionesExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-LESIONES.xlsx');
    }

    public function exportRoboModalidad ($rangos, $reporte) {
        $export = new RoboModalidadExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-ROBO-MODALIDAD.xlsm');
    }

    public function exportInformativo ($rangos, $reporte) {
        $export = new InformativoExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-INFORMATIVO.xlsm');
    }

    public function exportIncremento ($rangos, $reporte) {
        $export = new IncrementoExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-INCREMENTO.xlsm');
    }

    public function exportIncidencia ($rangos, $reporte) {
        $export = new IncidenciaExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-INCIDENCIA.xlsm');
    }

    public function exportDelitosModalidad ($rangos, $reporte) {
        $export = new DelitosModalidadExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-DELITOS-MODALIDAD.xlsm');
    }

    public function exportTrata ($rangos, $reporte) {
        $export = new TrataExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-TRATA.xlsm');
    }

    public function exportFeminicidios ($rangos, $reporte) {
        $export = new FeminicidiosExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-FEMINICIDIOS.xlsm');
    }

    public function exportInformativoAcumulado ($rangos, $reporte) {
        $export = new InformativoAcumuladoExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-INFORMATIVO-ACUMULADO.xlsm');
    }

    public function exportRoboModalidadMes ($rangos, $reporte) {
        $export = new RoboModalidadMesExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-ROBO-MODALIDAD-MES.xlsm');
    }

    public function exportGraficas ($rangos, $reporte) {
        $export = new GraficasExport($rangos, $reporte, $this->temporaryFileFactory);
        return Excel::download($export, 'PRUEBA-GRAFICAS.xlsm');
    }
}
