<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SecuestrosExport implements WithEvents
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
        $templatePath = storage_path('app/templates/4.-SECUESTROS.xlsm');
        if (file_exists($templatePath)) {
            $month = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
            $headers = [
                0 => [
                    'fisc' => 'APATZINGÁN',
                    'c' => 'B'
                ],
                1 => [
                    'fisc' => 'LÁZARO CÁRDENAS',
                    'c' => 'F'
                ],
                2 => [
                    'fisc' => 'MORELIA',
                    'c' => 'J'
                ],
                3 => [
                    'fisc' => 'URUAPAN',
                    'c' => 'N'
                ],
                4 => [
                    'fisc' => 'LA PIEDAD',
                    'c' => 'R'
                ],
                5 => [
                    'fisc' => 'ZAMORA',
                    'c' => 'V'
                ],
                6 => [
                    'fisc' => 'ZITÁCUARO',
                    'c' => 'Z'
                ],
                7 => [
                    'fisc' => 'COALCOMAN',
                    'c' => 'AD'
                ],
                8 => [
                    'fisc' => 'HUETAMO',
                    'c' => 'AH'
                ],
                9 => [
                    'fisc' => 'JIQUILPAN',
                    'c' => 'AL'
                ]
            ];
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $temporaryFile = $this->temporaryFile->makeLocal();
            IOFactory::createWriter($spreadsheet, 'Xlsx')->save($temporaryFile->getLocalPath());

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $sheet->setCellValue('B8', $year - 3);
            $sheet->setCellValue('C8', $year - 2);
            $sheet->setCellValue('D8', $year - 1);
            $sheet->setCellValue('E8', $year);

            $cad2 = $year - 3 . ' - ' . $year - 2 . ' - ' . $year - 1 . ' - ' . $year;
            $cad = ($mesInicial == $mesFinal) ? $month[$mesInicial - 1] : $month[$mesInicial - 1] . ' - ' . $month[$mesFinal - 1];
            $title = $cad . ' ' . $cad2;
            $sheet->setCellValue('A5', $title);

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal);
            //Log::info('--------------RESULTADOS-------------');
            //Log::info($resultados);

            foreach ($headers as $header) {
                //Log::info($header['fisc']);
                $dataFiscalia = $resultados->where('SUBPRO', $header['fisc']);//->values()->toArray();
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
                DB::raw("'SECUESTRO' as Secuestro"), // Valor fijo
                'AVE_MUNICIPIOS.ANIO',
                'AVE_MUNICIPIOS.MES',
                DB::raw('SUM(AVE_MUNICIPIOS.CANTIDAD) as CANTIDAD')
            )
            ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 3, $year])
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->whereIn('AVE_MUNICIPIOS.IDDELITO', ['1322', '1323', '1324', '1325', '1326', '1327', '2631', '2632', '2633', '1320', '1328', '1329'])
            ->groupBy('sb.SUBPRO', 'AVE_MUNICIPIOS.ANIO', 'AVE_MUNICIPIOS.MES')
            ->get();
        
        // Mostrar los resultados
        return $delitosCategorizados;
    }

    public function writeCell($value, $column, $row, $sum, $sheet) {
        // Convierte la columna a un índice numérico
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
        
        // Suma el valor
        $nextColumnIndex = $columnIndex + $sum;
        
        // Convierte el índice de vuelta a una cadena de caracteres
        $nextColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($nextColumnIndex);
        
        // Crea la referencia de la celda
        $cell = $nextColumn . $row;
        //Log::info('CELDA ' . $cell);
        // Escribe el valor en la celda
        $sheet->setCellValue($cell, $value);    
    }
    
}
