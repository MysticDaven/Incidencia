<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExtorsionesExport implements WithEvents
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
        $templatePath = storage_path('app/templates/5.-EXTORSIONES.xlsm');
        if (file_exists($templatePath)) {
            $headers = [
                0 => [
                    'fisc' => 'APATZINGÁN',
                    'c' => 'B'
                ],
                1 => [
                    'fisc' => 'LÁZARO CÁRDENAS',
                    'c' => 'E'
                ],
                2 => [
                    'fisc' => 'MORELIA',
                    'c' => 'H'
                ],
                3 => [
                    'fisc' => 'URUAPAN',
                    'c' => 'K'
                ],
                4 => [
                    'fisc' => 'LA PIEDAD',
                    'c' => 'N'
                ],
                5 => [
                    'fisc' => 'ZAMORA',
                    'c' => 'Q'
                ],
                6 => [
                    'fisc' => 'ZITÁCUARO',
                    'c' => 'T'
                ],
                7 => [
                    'fisc' => 'COALCOMAN',
                    'c' => 'W'
                ],
                8 => [
                    'fisc' => 'HUETAMO',
                    'c' => 'Z'
                ],
                9 => [
                    'fisc' => 'JIQUILPAN',
                    'c' => 'AC'
                ]                
            ];

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $sheet->setCellValue('B8', $year - 2);
            $sheet->setCellValue('C8', $year - 1);
            $sheet->setCellValue('D8', $year);

            $cad2 = $year - 2 . ' - ' . $year - 1 . ' - ' . $year;
            $cad = ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];

            $title = $cad . ' - ' . $cad2;
            $sheet->setCellValue('A5', $title);

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal);

            foreach ($headers as $header) {
                $dataFiscalia = $resultados->where('SUBPRO', $header['fisc']);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        $aniosDiferencia = $year - $data->{'ANIO'};
                        
                        if ($aniosDiferencia >= 0 && $aniosDiferencia <= 2) {
                            $column = $header['c'];
                            $row = (8 + $data->{'MES'});
                            $value = $data->{'CANTIDAD'};
                            $this->writeCell($value, $column, $row, self::I[$aniosDiferencia], $sheet);
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

    public function realizarConsulta ($year, $mesInicial, $mesFinal) {
        $delitosCategorizados = AveMunicipio::join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
            ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'm.IDSUBPRO2')
            ->select(
                'sb.SUBPRO',
                DB::raw("'EXTORSION' as Extorsion"),
                'AVE_MUNICIPIOS.ANIO',
                'AVE_MUNICIPIOS.MES',
                DB::raw('SUM(AVE_MUNICIPIOS.CANTIDAD) as CANTIDAD')
            )
            ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 2, $year])
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->whereIn('AVE_MUNICIPIOS.IDDELITO', self::EXTORSION)
            ->groupBy('sb.SUBPRO', 'AVE_MUNICIPIOS.ANIO', 'AVE_MUNICIPIOS.MES')
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
