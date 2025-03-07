<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use App\Models\Violencia;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GraficasExport implements WithEvents
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
        $templatePath = storage_path('app/templates/Graficas.xlsm');
        if (file_exists($templatePath)) {
            $delitos = [
                'VIOLENCIA_FAMILIAR' => 25,
                'EXTORSION' => 43,
                'HOMICIDIO_DOLOSO' => 62,
                'TRATA_PERSONAS' => 80,
                'NARCOMENUDEO' => 98,
                'SECUESTRO' => 170,
                'VIOLACION' => 188
            ];
            $robos = [
                'ROBO_HABITACION' => 116,
                'ROBO_TRANSEUNTE' => 134,
                'ROBO_VEHICULO' => 152
            ];         
            $graficas = [
                'S24' => 25,
                'S61' => 62,
                'S42' => 43,
                'S79' => 80,
                'S97' => 98,
                'S115' => 116,
                'S133' => 134,
                'S151' => 152,
                'S169' => 170,
                'S187' => 188
            ];

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];
            $period = ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];
            $sheet->setCellValue('W7', $period);
            $period .= ' ' . $year - 1 . ' - ' . $year;

            $sheet->setCellValue('A3', $period);
            $sheet->setCellValue('T25', $year - 3);
            $sheet->setCellValue('U25', $year - 2);
            $sheet->setCellValue('V25', $year - 1);
            $sheet->setCellValue('W25', $year);
            $sheet->setCellValue('S5', 1);

            foreach ($graficas as $celda => $v) {
                $sheet->setCellValue($celda, "W" . ($mesFinal + $v));
            }

            $resultados = $this->realizarConsulta($mesInicial, $mesFinal, $year, 0);
            $c = 'T';

            foreach ($delitos as $delito => $r) {
                $dataDelito = $resultados->where('Delito', $delito);
                if (count($dataDelito) > 0) {
                    foreach ($dataDelito as $data) {
                        $anios = $year - $data->{'ANIO'};
                        if ($anios >= 0 && $anios <= 3) {
                            $row = $data->{'MES'} + $r;
                            $value = $data->{'CANTIDAD'};
                            $this->writeCell($value, $c, $row, self::I2[$anios], $sheet);
                        }
                    }
                }
            }

            $resultados = $this->realizarConsulta($mesInicial, $mesFinal, $year, 1);

            foreach ($robos as $robo => $r) {
                $dataRobo = $resultados->where('Delito', $robo);
                if (count($dataRobo) > 0) {
                    foreach ($dataRobo as $data) {
                        $anios = $year - $data->{'ANIO'};
                        if ($anios >= 0 && $anios <= 3) {
                            $row = $data->{'MES'} + $r;
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
            $consulta = AveMunicipio::join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'AVE_MUNICIPIOS.IDSUBPRO')
                ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 3, $year])
                ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
                ->whereIn('AVE_MUNICIPIOS.IDDELITO', ['1160','1350', '161K','161L','1618','161C','161D','161H','161I','1671','1612','1614','1619','1617','161G', '0532', '2311','2312', '1324','1325','1326','2631','1320', '1412','1413','1410'])
                ->select(
                    DB::raw("$caseDelitos AS Delito"),
                    'AVE_MUNICIPIOS.ANIO',
                    'AVE_MUNICIPIOS.MES',
                    DB::raw('SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD')
                )
                ->groupBy(
                    'AVE_MUNICIPIOS.ANIO',
                    'AVE_MUNICIPIOS.MES',
                    DB::raw($caseDelitos)
                )
                ->get();
        }
        else {
            $consulta = Violencia::whereBetween('VIOLENCIA.ANIO', [$year - 3, $year])
                ->whereBetween('VIOLENCIA.MES', [$mesInicial, $mesFinal])
                ->whereIn('VIOLENCIA.DELITO', ['1819', '182A','1818','182B','182L', '1814'])
                ->select(
                    DB::raw("$caseRobo AS Delito"),
                    'VIOLENCIA.ANIO',
                    'VIOLENCIA.MES',
                    DB::raw('SUM(VIOLENCIA.CANTIDAD) AS CANTIDAD')
                )
                ->groupBy(
                    'VIOLENCIA.ANIO',
                    'VIOLENCIA.MES',
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
