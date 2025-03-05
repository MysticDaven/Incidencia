<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PrincipalesDelitosMesExport implements WithEvents
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
        $templatePath = storage_path('app/templates/INCIDENCIA_ESTATAL.xlsm');
        if (file_exists($templatePath)) {
            $headers = [
                0 => [
                    'fisc' => 'APATZINGÁN',
                    'r' => 52
                ],
                1 => [
                    'fisc' => 'LÁZARO CÁRDENAS',
                    'r' => 96
                ],
                2 => [
                    'fisc' => 'MORELIA',
                    'r' => 140
                ],
                3 => [
                    'fisc' => 'URUAPAN',
                    'r' => 184
                ],
                4 => [
                    'fisc' => 'LA PIEDAD',
                    'r' => 228
                ],
                5 => [
                    'fisc' => 'ZAMORA',
                    'r' => 272
                ],
                6 => [
                    'fisc' => 'ZITÁCUARO',
                    'r' => 316
                ],
                7 => [
                    'fisc' => 'COALCOMAN',
                    'r' => 360
                ],
                8 => [
                    'fisc' => 'HUETAMO',
                    'r' => 404
                ],
                9 => [
                    'fisc' => 'JIQUILPAN',
                    'r' => 448
                ]                
            ];  
            $delitos = ['ROBO', 'ROBO DE VEHÍCULO', 'LESIONES DOLOSAS', 'HOMICIDIO CULPOSO', 'NARCOMENUDEO', 'VIOLENCIA FAMILIAR', 'LESIONES CULPOSAS', 'RECEPTACIÓN', 'HOMICIDIO DOLOSO', 'DAÑO CULPOSO', 'DAÑO DOLOSO', 'FRAUDE', 'AMENAZAS', 'DESPOJO', 'VIOLACIÓN', 'ABIGEATO', 'ABUSO SEXUAL', 'PRIVACION DE LA LIBERTAD', 'ABUSO DE AUTORIDAD', 'FALSIFICACION DE DOCUMENTOS', 'ABUSO DE CONFIANZA', 'ALLANAMIENTO DE MORADA', 'OBLIGACION ALIMENTARIA', 'SUSTRACCION DE PERSONA', 'DELITOS ECOLOGIA', 'ARMAS PROHIBIDAS', 'ESTUPRO', 'EXTORSION', 'HOSTIGAMIENTO SEXUAL', 'CONDUCTORES DE VEHICULOS', 'SECUESTRO', 'TRATA DE PERSONAS', 'TURISMO SEXUAL', 'OTRO DELITO'];

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $period = ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];
            $period =  $period . ' ' . $year;
            $sheet->setCellValue('A4', $period);

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal, 1);
            // Log::info('------------------RESULTADOS TOTAL-------------------' . $year);
            // Log::info($resultados);
            $c = 'b';
            foreach ($resultados as $data) {
                $row = array_search($data->{'Delito'}, $delitos) + 8;
                $value = $data->{'CANTIDAD'};
                //Log::info('ROW: ' . array_search($data->{'Delito'}, $delitos));
                $this->writeCell($value, $c, $row, $data->{'MES'}, $sheet);
            }

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal, 0);
            // Log::info('------------------RESULTADOS FISCALIA-------------------' . $year);
            // Log::info($resultados);
            foreach ($headers as $header) {
                $dataFiscalia = $resultados->where('SUBPRO', $header['fisc']);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        $row = array_search($data->{'Delito'}, $delitos) + $header['r'];
                        $value = $data->{'CANTIDAD'};
                        $this->writeCell($value, $c, $row, $data->{'MES'}, $sheet);
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
            'AMENAZAS' => self::AMENAZAS,
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
            'SECUESTRO' => self::SECUESTRO,
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
            ->join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
            ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'm.IDSUBPRO2')
            ->where('AVE_MUNICIPIOS.ANIO', $year)
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('DELITOS AS d')
                    ->whereColumn('d.IDDELITO', 'AVE_MUNICIPIOS.IDDELITO')
                    ->where('d.ESTATUS', 1);
            })
            ->select(
                'sb.SUBPRO',
                DB::raw("$caseDelitos AS Delito"),
                'AVE_MUNICIPIOS.MES',
                DB::raw('SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD')
            )
            ->groupBy(
                'sb.SUBPRO',
                'AVE_MUNICIPIOS.MES',
                DB::raw($caseDelitos)
            )
            ->get();
        }
        else {
            $delitos = AveMunicipio::leftJoin('DELITOS as d', 'AVE_MUNICIPIOS.IDDELITO', '=', 'd.IDDELITO')
            ->join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
            ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'm.IDSUBPRO2')
            ->where('AVE_MUNICIPIOS.ANIO', $year)
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('DELITOS AS d')
                    ->whereColumn('d.IDDELITO', 'AVE_MUNICIPIOS.IDDELITO')
                    ->where('d.ESTATUS', 1);
            })
            ->select(
                //'sb.SUBPRO',
                DB::raw("$caseDelitos AS Delito"),
                'AVE_MUNICIPIOS.MES',
                DB::raw('SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD')
            )
            ->groupBy(
                //'sb.SUBPRO',
                'AVE_MUNICIPIOS.MES',
                DB::raw($caseDelitos)
            )
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
