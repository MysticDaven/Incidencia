<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use App\Models\Violencia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AltoImpactoExport implements WithEvents
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
        $templatePath = storage_path('app/templates/3.-Alto_Impacto.xlsm');
        if (file_exists($templatePath)) {
            $headers = [
                'APATZINGÁN' => 'C',
                'LÁZARO CÁRDENAS' => 'P',
                'MORELIA' => 'AC',
                'URUAPAN' => 'AP',
                'LA PIEDAD' => 'BC',
                'ZAMORA' => 'BP',
                'ZITÁCUARO' => 'CC',
                'COALCOMAN' => 'CP',
                'HUETAMO' => 'DC',
                'JIQUILPAN' => 'DP'
            ];
            $delitos = ['VIOLENCIA_FAMILIAR', 'EXTORSION', 'HOMICIDIO_DOLOSO', 'TRATA_PERSONAS', 'NARCOMENUDEO', 'ROBO_HABITACION', 'ROBO_TRANSEUNTE', 'ROBO_VEHICULO', 'SECUESTRO', 'VIOLACION'];

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];
            $period = 'COMPARATIVO ';
            $period .= ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];
            $period .= ' ' . $year - 3 . ' - ' . $year;

            $sheet->setCellValue('B6', $period);            
            $sheet->setCellValue('C9', $year - 3);
            $sheet->setCellValue('D9', $year - 2);
            $sheet->setCellValue('E9', $year - 1);
            $sheet->setCellValue('F9', $year);

            $resultados = $this->realizarConsulta($mesInicial, $mesFinal, $year, 0);
            $r = 10;
            foreach ($headers as $header => $c) {
                $dataFiscalia = $resultados->where('SUBPRO', $header);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        $anios = $year - $data->{'ANIO'};
                        if ($anios >= 0 && $anios <= 3) {
                            $row = array_search($data->{'Delito'}, $delitos) + $r;
                            $value = $data->{'CANTIDAD'};
                            $this->writeCell($value, $c, $row, self::I2[$anios], $sheet);
                        }
                    }
                }
            }
            $resultados = $this->realizarConsulta($mesInicial, $mesFinal, $year, 1);
            foreach ($headers as $header => $c) {
                $dataFiscalia = $resultados->where('SUBPRO', $header);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        $anios = $year - $data->{'ANIO'};
                        if ($anios >= 0 && $anios <= 3) {
                            $row = array_search($data->{'Delito'}, $delitos) + $r;
                            $value = $data->{'CANTIDAD'};
                            $this->writeCell($value, $c, $row, self::I2[$anios], $sheet);
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

    public function realizarConsulta ($mesInicial, $mesFinal, $year, $value) {
        $delitos = [
            'VIOLENCIA_FAMILIAR' => self::VIOLENCIA_FAMILIAR,
            'EXTORSION' => self::EXTORSION,
            'HOMICIDIO_DOLOSO' => self::HOMICIDIO_DOLOSO_FEMINICIDIO,
            'TRATA_PERSONAS' => self::TRATA_PERSONAS,
            'NARCOMENUDEO' => self::NARCOMENUDEO,
            'SECUESTRO' => self::SECUESTRO,
            'VIOLACION' => self::VIOLACION
        ];

        $robos = [
            'ROBO_HABITACION' => self::AROBO_HABITACION,
            'ROBO_TRANSEUNTE' => self::AROBO_TRANSEUNTES,
            'ROBO_VEHICULO' => self::AROBO_VEHICULO
        ];

        $caseDelitos = "CASE ";
        foreach ($delitos as $delito => $id) {
            $caseDelitos .= "WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . implode("','", $id) . "') THEN '$delito' ";
        }
        $caseDelitos .= " END";
        $caseRobo = "CASE ";
        foreach ($robos as $robo => $id) {
            $caseRobo .= "WHEN VIOLENCIA.DELITO IN ('" . implode("','", $id) . "') THEN '$robo' ";
        }
        $caseRobo .= " END";
        
        if ($value == 0) {
            $consulta = AveMunicipio::join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
                ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'm.IDSUBPRO2')
                ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 3, $year])
                ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
                ->whereIn('AVE_MUNICIPIOS.IDDELITO', ['1160', '1350', '161K','161L','1618','161C','161D','161H','161I','1671','1612','1614','1619','1617','161G', '0532', '2311','2312', '1322','1323','1324','1325','1326','1327','2631','2632','2633','1320','1328','1329', '1412','1413','1410'])
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('DELITOS AS d')
                        ->whereColumn('d.IDDELITO', 'AVE_MUNICIPIOS.IDDELITO')
                        ->where('d.ESTATUS', 1);
                })
                ->select(
                    'sb.SUBPRO',
                    DB::raw("$caseDelitos AS Delito"),
                    'AVE_MUNICIPIOS.ANIO',
                    DB::raw('SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD')
                )
                ->groupBy(
                    'sb.SUBPRO',
                    'AVE_MUNICIPIOS.ANIO',
                    DB::raw($caseDelitos)
                )
                ->get();
        }
        else {
            $consulta = Violencia::join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'VIOLENCIA.IDMUNICIPIO')
                ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'm.IDSUBPRO2')
                ->whereBetween('VIOLENCIA.ANIO', [$year - 3, $year])
                ->whereBetween('VIOLENCIA.MES', [$mesInicial, $mesFinal])
                ->whereIn('VIOLENCIA.DELITO', ['1819', '182A', '1818', '182B', '182L', '1814'])
                ->select(
                    'sb.SUBPRO',
                    DB::raw("$caseRobo AS Delito"),
                    'VIOLENCIA.ANIO',
                    DB::raw('SUM(VIOLENCIA.CANTIDAD) AS CANTIDAD')
                )
                ->groupBy(
                    'sb.SUBPRO',
                    'VIOLENCIA.ANIO',
                    DB::raw($caseRobo)
                )
                ->get();
        }

        return $consulta;
    }

    public function writeCell ($value, $column, $row, $sum, $sheet) {
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
        $nextColumnIndex = $columnIndex + $sum;
        $nextColumn =  \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($nextColumnIndex);

        $cell = $nextColumn . $row;
        $sheet->setCellValue($cell, $value);
    }    
}
