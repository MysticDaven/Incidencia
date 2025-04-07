<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HomicidiosComparativoExport implements WithEvents
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

    public function registerEvents (): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                $this->beforeWriting($event);
            }
        ];
    }

    public function beforeWriting (BeforeWriting $event) {
        $templatePath = storage_path('app/templates/6.HOMICIDIOS.xlsm');
        if (file_exists($templatePath)) {
            $headers = [
                0 => [
                    'fisc' => 'APATZINGÁN',
                    'c' => 'B'
                ],
                1 => [
                    'fisc' => 'LÁZARO CÁRDENAS',
                    'c' => 'G'
                ],
                2 => [
                    'fisc' => 'MORELIA',
                    'c' => 'L'
                ],
                3 => [
                    'fisc' => 'URUAPAN',
                    'c' => 'Q'
                ],
                4 => [
                    'fisc' => 'LA PIEDAD',
                    'c' => 'V'
                ],
                5 => [
                    'fisc' => 'ZAMORA',
                    'c' => 'AA'
                ],
                6 => [
                    'fisc' => 'ZITÁCUARO',
                    'c' => 'AF'
                ],
                7 => [
                    'fisc' => 'COALCOMAN',
                    'c' => 'AK'
                ],
                8 => [
                    'fisc' => 'HUETAMO',
                    'c' => 'AP'
                ],
                9 => [
                    'fisc' => 'JIQUILPAN',
                    'c' => 'AU'
                ]                
            ];
            $homicidios = [
                'DOLOSO' => 10,
                'DOLOSO AB' => 11,
                'DOLOSO AF' => 12,
                'CULPOSO' => 14,
                'CULPOSO AB' => 15,
                'CULPOSO AF' => 16
            ];
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $cad = $year - 2 . ' - ' . $year;
            $title = ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];
            $title = $title . ' ' . $cad;

            $sheet->setCellValue('B5', $title);
            $sheet->setCellValue('B8', $year - 2);
            $sheet->setCellValue('C8', $year - 1);
            $sheet->setCellValue('D8', $year);

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal);

            foreach ($headers as $header) {
                $dataFiscalia = $resultados->where('SUBPRO', $header['fisc']);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        $anios = $year - $data->{'ANIO'};
                        if ($anios >= 0 && $anios <= 2) {
                            $column = $header['c'];
                            $row = $homicidios[$data->{'Delito'}];
                            $value = $data->{'CANTIDAD'};
                            $this->writeCell($value, $column, $row, self::I[$anios], $sheet);
                        }

                    }
                }
            }

            $outputPath = storage_path('app/exports/PRUEBA-HOMICIDIOS-COMPARATIVO.xlsm');
            IOFactory::createWriter($spreadsheet, 'Xlsx')->save($outputPath);            
        }
        else {
            throw new \Exception("La plantilla no existe en la ruta especificada: {$templatePath}");
        }
    }

    public function realizarConsulta ($year, $mesInicial, $mesFinal) {        
        $delitosCategorizados = AveMunicipio::join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
            ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'm.IDSUBPRO2')
            ->select(
                'sb.SUBPRO',
                DB::raw("
                    CASE
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161G','1617','1614','1618','1619','161I','1612','161H','1671') THEN 'DOLOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161L','161D') THEN 'DOLOSO AB'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161K','161C') THEN 'DOLOSO AF'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161J','1613','1615','1616','161E') THEN 'CULPOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161B') THEN 'CULPOSO AB'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161A') THEN 'CULPOSO AF'                        
                    END AS Delito
                "),
                'AVE_MUNICIPIOS.ANIO',
                DB::raw("SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD")
            )
            ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 2, $year])
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->whereIn('AVE_MUNICIPIOS.IDDELITO', ['161G','1617','1614','1618','1619','161I','1612','161H','1671', '161L','161D', '161K','161C', '161J','1613','1615','1616','161E', '161B', '161A'])
            ->groupBy(
                'sb.SUBPRO',
                'AVE_MUNICIPIOS.ANIO',
                DB::raw("
                    CASE 
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161G','1617','1614','1618','1619','161I','1612','161H','1671') THEN 'DOLOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161L','161D') THEN 'DOLOSO AB'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161K','161C') THEN 'DOLOSO AF'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161J','1613','1615','1616','161E') THEN 'CULPOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161B') THEN 'CULPOSO AB'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('161A') THEN 'CULPOSO AF'
                    END
                ")
            )
            ->get();

        return $delitosCategorizados;
    }

    public function writeCell ($value, $column, $row, $sum, $sheet) {
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
        $nextColumnIndex = $columnIndex + $sum;
        $nextColumn =  \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($nextColumnIndex);

        $cell = $nextColumn . $row;
        $sheet->setCellValue($cell, $value);        
    }
}
