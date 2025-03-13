<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use App\Models\Faltante;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InformativoAcumuladoExport implements WithEvents
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
        $templatePath = storage_path('app/templates/10.-informativo.xlsm');
        if (file_exists($templatePath)) {
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $period = 'INFORMATIVO ';
            $period .= ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];
            $period .= ' 2001 ' . $year;
            $sheet->setCellValue('A4', $period);

            if ($year <= 2016) {
                $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal, 0);
                $this->llenarConsulta($resultados, $year, $sheet);
            }
            else {
                $resultados = $this->realizarConsulta(2016, $mesInicial, $mesFinal, 0);
                $this->llenarConsulta($resultados, 2016, $sheet);

                $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal, 1);
                $this->llenarConsulta($resultados, $year, $sheet);
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
        if ($value == 0) {
            $delitosCategorias = [
                'ROBO' => 26,
                'ROBO DE VEHÍCULO' => 27,
                'LESIONES DOLOSAS' => 22,
                'HOMICIDIO CULPOSO' => 20,
                'NARCOMENUDEO' => 8,
                'VIOLENCIA FAMILIAR' => 11,
                'LESIONES CULPOSAS' => 23,
                'RECEPTACIÓN' => 25,
                'HOMICIDIO DOLOSO' => 19,
                'DAÑO CULPOSO' => 10,
                'DAÑO DOLOSO' => 9,
                'FRAUDE' => 18,
                'AMENAZAS' => 5,
                'DESPOJO' => 13,
                'VIOLACIÓN' => 32,
                'ABIGEATO' => 1,
                'ABUSO SEXUAL' => 4,
                'PRIVACION DE LA LIBERTAD' => 24,
                'ABUSO DE AUTORIDAD' => 2,
                'FALSIFICACION DE DOCUMENTOS' => 17,
                'ABUSO DE CONFIANZA' => 3,
                'ALLANAMIENTO DE MORADA' => 33,
                'OBLIGACION ALIMENTARIA' => 21,
                'SUSTRACCION DE PERSONA' => 29,
                'DELITOS ECOLOGIA' => 14,
                'ARMAS PROHIBIDAS' => 6,
                'ESTUPRO' => 15,
                'EXTORSION' => 16,
                'HOSTIGAMIENTO SEXUAL' => 7,
                'CONDUCTORES DE VEHICULOS' => 12,
                'SECUESTRO' => 28,
                'TRATA DE PERSONAS' => 31,
                'TURISMO SEXUAL' => 30,
                'OTRO DELITO' => 34
            ];

            $caseDelitos = "CASE ";
            foreach ($delitosCategorias as $categoria => $id) {
                $caseDelitos .= "WHEN faltantes.IDDELITO = '$id' THEN '$categoria' ";
            }
            $caseDelitos .= " END";

            $delitos = Faltante::whereBetween('faltantes.ANIO', [2001, $year])
                ->whereBetween('faltantes.MES', [$mesInicial, $mesFinal])
                ->selectRaw("
                    faltantes.fiscalia,
                    $caseDelitos AS Delito,
                    faltantes.ANIO,
                    SUM(faltantes.CANTIDAD) AS CANTIDAD
                ")
                ->groupBy('faltantes.fiscalia', 'faltantes.ANIO',
                    DB::raw("$caseDelitos")
                )
                ->get();            
        }
        else {
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

            $delitos = AveMunicipio::leftJoin('DELITOS as d', 'AVE_MUNICIPIOS.IDDELITO', '=', 'd.IDDELITO')
            ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'AVE_MUNICIPIOS.IDSUBPRO')
            ->whereBetween('AVE_MUNICIPIOS.ANIO', [2017, $year])
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

    public function llenarConsulta ($resultados, $year, $sheet) {
        $delitos = ['ROBO', 'ROBO DE VEHÍCULO', 'LESIONES DOLOSAS', 'HOMICIDIO CULPOSO', 'NARCOMENUDEO', 'VIOLENCIA FAMILIAR', 'LESIONES CULPOSAS', 'RECEPTACIÓN', 'HOMICIDIO DOLOSO', 'DAÑO CULPOSO', 'DAÑO DOLOSO', 'FRAUDE', 'AMENAZAS', 'DESPOJO', 'VIOLACIÓN', 'ABIGEATO', 'ABUSO SEXUAL', 'PRIVACION DE LA LIBERTAD', 'ABUSO DE AUTORIDAD', 'FALSIFICACION DE DOCUMENTOS', 'ABUSO DE CONFIANZA', 'ALLANAMIENTO DE MORADA', 'OBLIGACION ALIMENTARIA', 'SUSTRACCION DE PERSONA', 'DELITOS ECOLOGIA', 'ARMAS PROHIBIDAS', 'ESTUPRO', 'EXTORSION', 'HOSTIGAMIENTO SEXUAL', 'CONDUCTORES DE VEHICULOS', 'SECUESTRO', 'TRATA DE PERSONAS', 'TURISMO SEXUAL', 'OTRO DELITO'];   
        $c = 'c';
        $aux = $year - 2001;

        if ($year <= 2016) {
            $headers = [
                1 => 52,
                2 => 96,
                3 => 140,
                4 => 184,
                8 => 228,
                5 => 272,
                6 => 316
            ];

            foreach ($headers as $header => $fila) {
                $dataFiscalia = $resultados->where('fiscalia', $header);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        $anios = $data->{'ANIO'} - $year;
                        $sum = $anios + $aux;
                        $row = array_search($data->{'Delito'}, $delitos) + $fila;
                        $value = $data->{'CANTIDAD'};
                        $this->writeCell($value, $c, $row, $sum, $sheet);
                    }
                }
            }            
        }
        else {
            $headers = [
                'APATZINGÁN' => 52,
                'LÁZARO CÁRDENAS' => 96,
                'MORELIA' => 140,
                'URUAPAN' => 184,
                'LA PIEDAD' => 228,
                'ZAMORA' => 272,
                'ZITÁCUARO' => 316,
                'COALCOMAN' => 360,
                'HUETAMO' => 403,
                'JIQUILPAN' => 446
            ];

            foreach ($headers as $header => $fila) {
                $dataFiscalia = $resultados->where('SUBPRO', $header);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        $anios = $data->{'ANIO'} - $year;
                        $sum = $anios + $aux;
                        $row = array_search($data->{'Delito'}, $delitos) + $fila;
                        $value = $data->{'CANTIDAD'};
                        $this->writeCell($value, $c, $row, $sum, $sheet);
                    }
                }
            }                        
        }
    }

    public function writeCell ($value, $column, $row, $sum, $sheet) {
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
        $nextColumnIndex = $columnIndex + $sum;
        $nextColumn =  \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($nextColumnIndex);

        $cell = $nextColumn . $row;
        $sheet->setCellValue($cell, $value);        
    }    
}
