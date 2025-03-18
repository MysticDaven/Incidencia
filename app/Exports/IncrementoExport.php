<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IncrementoExport implements WithEvents
{
    use DelitosTrait;
    protected $rangos;
    protected $reporte;
    protected $temporaryFile;

    public function __construct($rangos, $reporte, $temporaryFile)
    {
        $this->rangos = $rangos;
        $this->reporte = $reporte;
        $this->temporaryFile = $temporaryFile;
    }

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                $this->beforeWriting($event);
            }
        ];
    }

    public function beforeWriting (BeforeWriting $event) {
        $templatePath = storage_path('app/templates/12.-Incremento.xlsm'); 
        if (file_exists($templatePath)) {
            $headers = [
                'APATZINGÁN' => 51,
                'LÁZARO CÁRDENAS' => 94,
                'MORELIA' => 137,
                'URUAPAN' => 180,
                'LA PIEDAD' => 223,
                'ZAMORA' => 266,
                'ZITÁCUARO' => 309,
                'COALCOMAN' => 352,
                'HUETAMO' => 395,
                'JIQUILPAN' => 438
            ];
            $delitos = [
                'ROBO' => 'ROBO',
                'ROBO DE VEHÍCULO' => 'ROBO DE VEHÍCULO',
                'LESIONES DOLOSAS' => 'LESIONES DOLOSAS',
                'HOMICIDIO CULPOSO' => 'HOMICIDIO CULPOSO',
                'NARCOMENUDEO' => 'NARCOMENUDEO',
                'VIOLENCIA FAMILIAR' => 'VIOLENCIA FAMILIAR',
                'LESIONES CULPOSAS' => 'LESIONES CULPOSAS',
                'RECEPTACIÓN' => 'RECEPTACIÓN',
                'HOMICIDIO DOLOSO' => 'HOMICIDIO DOLOSO',
                'DAÑO CULPOSO' => 'DAÑO EN LAS COSAS CULPOSO',
                'DAÑO DOLOSO' => 'DAÑO EN LAS COSAS DOLOSO',
                'FRAUDE' => 'FRAUDE',
                'AMENAZAS' => 'AMENAZAS',
                'DESPOJO' => 'DESPOJO',
                'VIOLACIÓN' => 'VIOLACIÓN',
                'ABIGEATO' => 'ABIGEATO',
                'ABUSO SEXUAL' => 'ABUSO SEXUAL',
                'PRIVACION DE LA LIBERTAD' => 'PRIVACIÓN DE LA LIBERTAD PERSONAL',
                'ABUSO DE AUTORIDAD' => 'ABUSO DE AUTORIDAD Y USO ILEGAL DE LA FUERZA PÚBLICA',
                'FALSIFICACION DE DOCUMENTOS' => 'FALSIFICACIÓN Y USO DE DOCUMENTOS FALSOS',
                'ABUSO DE CONFIANZA' => 'ABUSO DE CONFIANZA',
                'ALLANAMIENTO DE MORADA' => 'ALLANAMIENTO DE MORADA',
                'OBLIGACION ALIMENTARIA' => 'INCUMPLIMIENTO DE LA OBLIGACIÓN ALIMENTARIA',
                'SUSTRACCION DE PERSONA' => 'RETENCIÓN O SUSTRACCIÓN DE PERSONA',
                'DELITOS ECOLOGIA' => 'DELITOS CONTRA LA ECOLOGÍA',
                'ARMAS PROHIBIDAS' => 'ARMAS PROHIBIDAS',
                'ESTUPRO' => 'ESTUPRO',
                'EXTORSION' => 'EXTORSIÓN',
                'HOSTIGAMIENTO SEXUAL' => 'HOSTIGAMIENTO SEXUAL',
                'CONDUCTORES DE VEHICULOS' => 'DEL. COMETIDOS POR CONDUCTORES DE VEHÍCULOS',
                'SECUESTRO' => 'SECUESTRO',
                'TRATA DE PERSONAS' => 'TRATA DE PERSONAS',
                'TURISMO SEXUAL' => 'TURISMO SEXUAL'
            ];
            $delitos2 = ['ROBO', 'ROBO DE VEHÍCULO', 'LESIONES DOLOSAS', 'HOMICIDIO CULPOSO', 'NARCOMENUDEO', 'VIOLENCIA FAMILIAR', 'LESIONES CULPOSAS', 'RECEPTACIÓN', 'HOMICIDIO DOLOSO', 'DAÑO CULPOSO', 'DAÑO DOLOSO', 'FRAUDE', 'AMENAZAS', 'DESPOJO', 'VIOLACIÓN', 'ABIGEATO', 'ABUSO SEXUAL', 'PRIVACION DE LA LIBERTAD', 'ABUSO DE AUTORIDAD', 'FALSIFICACION DE DOCUMENTOS', 'ABUSO DE CONFIANZA', 'ALLANAMIENTO DE MORADA', 'OBLIGACION ALIMENTARIA', 'SUSTRACCION DE PERSONA', 'DELITOS ECOLOGIA', 'ARMAS PROHIBIDAS', 'ESTUPRO', 'EXTORSION', 'HOSTIGAMIENTO SEXUAL', 'CONDUCTORES DE VEHICULOS', 'SECUESTRO', 'TRATA DE PERSONAS', 'TURISMO SEXUAL', 'OTRO DELITO'];  

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $period = 'COMPARATIVO DE ';
            $period .= ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];
            $period .= ' ' . $year - 1 . ' - ' . $year;

            $sheet->setCellValue('A4', $period);
            $sheet->setCellValue('C7', $year - 1);
            $sheet->setCellValue('D7', $year);
            $sheet->setCellValue('L3', 1);

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal, 0);
            $c = 'c';
            $r = 8;
            
            foreach ($delitos as $delito => $name) {
                $dataFiscalia = $resultados->where('Delito', $delito)->sortBy('ANIO');
                if (count($dataFiscalia) > 0) {
                    $aux1 = 0;
                    $aux2 = 0;
                    $i = 1;
                    foreach ($dataFiscalia as $data) {
                        $delitoRow = array_search($data->{'Delito'}, $delitos2);
                        if ($data->{'ANIO'} == $year - 1){
                            $aux1 = $data->{'CANTIDAD'};
                        }
                        else{
                            $aux2 = $data->{'CANTIDAD'};
                        }
                    }
                    if ($aux1 < $aux2) {
                        $row = $delitoRow + $r;
                        $this->writeCell($aux1, $c, $row, 0, $sheet);
                        $this->writeCell($aux2, $c, $row, 1, $sheet);
                        $sheet->setCellValue('B' . $row, $name);
                    }
                }
            }

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal, 1);

            $dataDelitos = $resultados->where('Delito', 'ROBO')->where('SUBPRO', 'APATZINGÁN')->sortBy(['SUBPRO', 'ANIO']);
            
            foreach ($headers as $header => $celda) {
                foreach ($delitos as $delito => $name) {
                    $dataFiscalia = $resultados->where('SUBPRO', $header)->where('Delito', $delito)->sortBy(['SUBPRO', 'ANIO']);
                    if (count($dataFiscalia) > 0) {
                        $aux1 = 0;
                        $aux2 = 0;
                        foreach ($dataFiscalia as $data) {
                            $delitoRow = array_search($data->{'Delito'}, $delitos2);
                            if ($data->{'ANIO'} == $year - 1) {
                                $aux1 = $data->{'CANTIDAD'};
                            }
                            else {
                                $aux2 = $data->{'CANTIDAD'};
                            }                            
                        }
                        if ($aux1 < $aux2) {
                            if($aux1 > 0 && $aux2 > 0) {
                                $row = $delitoRow + $celda;
                                $this->writeCell($aux1, $c, $row, 0, $sheet);
                                $this->writeCell($aux2, $c, $row, 1, $sheet);
                                $sheet->setCellValue('B' . $row, $name);
                            }
                        }         
                    }
                }
            }

            $temporaryFile = $this->temporaryFile->makeLocal();
            IOFactory::createWriter($spreadsheet, 'Xlsx')->save($temporaryFile->getLocalPath());

            $event->writer->reopen($temporaryFile, \Maatwebsite\Excel\Excel::XLSX);
            $event->writer->getSheetByIndex(0);
        }
        else {
            throw new \Exception("La plantilla no existe en la ruta especificada: {$templatePath}");
        }
    }

    public function realizarConsulta ($year, $mesInicial, $mesFinal, $value) {
        $delitosCategorias = [
            'ROBO' => self::ROBOS,
            'ROBO DE VEHÍCULO' => self::ROBO_VEHICULO,
            'LESIONES DOLOSAS' => self::LESIONES_DOLOSAS,
            'HOMICIDIO CULPOSO' => self::HOMICIDIO_CULPOSO,
            'NARCOMENUDEO' => self::NARCOMENUDEO,
            'VIOLENCIA FAMILIAR' => self::VIOLENCIA_FAMILIAR,
            'LESIONES CULPOSAS' => self::LESIONES_CULPOSAS,
            'RECEPTACIÓN' => self::RECEPTACION,
            'HOMICIDIO DOLOSO' => self::HOMICIDIO_DOLOSO,
            'DAÑO CULPOSO' => self::DANIO_CULPOSO,
            'DAÑO DOLOSO' => self::DANIO_DOLOSO,
            'FRAUDE' => self::FRAUDE,
            'AMENAZAS' => self::AMENAZAS_V2,
            'DESPOJO' => self::DESPOJO,
            'VIOLACIÓN' => self::VIOLACION,
            'ABIGEATO' => self::ABIGEATO,
            'ABUSO SEXUAL' => self::ABUSO_SEXUAL,
            'PRIVACION DE LA LIBERTAD' => self::PRIVACION_LIBERTAD,
            'ABUSO DE AUTORIDAD' => self::ABUSO_AUTORIDAD,
            'FALSIFICACION DE DOCUMENTOS' => self::FALSIFICACION_DOCUMENTOS,
            'ABUSO DE CONFIANZA' => self::ABUSO_CONFIANZA,
            'ALLANAMIENTO DE MORADA' => self::ALLANAMIENTO_MORADA,
            'OBLIGACION ALIMENTARIA' => self::OBLIGACION_ALIMENTARIA,
            'SUSTRACCION DE PERSONA' => self::SUSTRACCION_PERSONA,
            'DELITOS ECOLOGIA' => self::DELITOS_ECOLOGIA,
            'ARMAS PROHIBIDAS' => self::ARMAS_PROHIBIDAS,
            'ESTUPRO' => self::ESTUPRO,
            'EXTORSION' => self::EXTORSION,
            'HOSTIGAMIENTO SEXUAL' => self::HOSTIGAMIENTO_SEXUAL,
            'CONDUCTORES DE VEHICULOS' => self::CONDUCTORES_VEHICULOS,
            'SECUESTRO' => self::SECUESTRO_V2,
            'TRATA DE PERSONAS' => self::TRATA_PERSONAS,
            'TURISMO SEXUAL' => self::TURISMO_SEXUAL
        ];        
             
        $caseDelitos = "CASE ";
        foreach ($delitosCategorias as $categoria => $id) {
            $caseDelitos .= "WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . implode("','", $id) . "') THEN '$categoria' ";
        }
        $caseDelitos .= "ELSE 'OTRO DELITO' END";

        if ($value == 0) {            
            $delitos = AveMunicipio::leftJoin('DELITOS as d', 'AVE_MUNICIPIOS.IDDELITO', '=', 'd.IDDELITO')
            ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'AVE_MUNICIPIOS.IDSUBPRO')
            ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 1, $year])
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('DELITOS AS d')
                    ->whereColumn('d.IDDELITO', 'AVE_MUNICIPIOS.IDDELITO')
                    ->where('d.ESTATUS', 1);
            })
            ->selectRaw("
                $caseDelitos AS Delito,
                AVE_MUNICIPIOS.ANIO,
                SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD    
            ")
            ->groupBy('AVE_MUNICIPIOS.ANIO', 
                DB::raw("$caseDelitos"))
            ->get();
        }
        else{
            $delitos = AveMunicipio::leftJoin('DELITOS as d', 'AVE_MUNICIPIOS.IDDELITO', '=', 'd.IDDELITO')
            ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'AVE_MUNICIPIOS.IDSUBPRO')
            ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 1, $year])
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('DELITOS AS d')
                    ->whereColumn('d.IDDELITO', 'AVE_MUNICIPIOS.IDDELITO')
                    ->where('d.ESTATUS', 1);
            })
            ->selectRaw("
                sb.SUBPRO,
                $caseDelitos AS Delito,
                AVE_MUNICIPIOS.ANIO,
                SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD    
            ")
            ->groupBy('sb.SUBPRO', 'AVE_MUNICIPIOS.ANIO', 
                DB::raw("$caseDelitos"))
            ->get();
        }

        return $delitos;
    }

    public function writeCell ($value, $column, $row, $sum, $sheet) {
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
        $nextColumnIndex = $columnIndex + $sum;
        $nextColumn =  \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($nextColumnIndex);

        $cell = $nextColumn . $row;
        $sheet->setCellValue($cell, $value);        
    } 
}
