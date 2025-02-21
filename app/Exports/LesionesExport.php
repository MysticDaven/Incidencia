<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LesionesExport implements WithEvents
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
        $templatePath = storage_path('app/templates/6.-HOMICIDIOS2.xlsx');
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

            $title = ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] . ' ' . $year : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1] . ' ' . $year;
            $sheet->setCellValue('A5', $title);
            $sheet->setCellValue('A4', 'LESIONES POR AVERIGUACIÓN PREVIA Y CARPETA DE INVESTIGACIÓN REGISTRADAS EN EL ESTADO');

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal);
            // Log::info('------------RESULTADOS--------------');
            // Log::info($resultados);

            foreach ($headers as $header) {
                $dataFiscalia = $resultados->where('SUBPRO', $header['fisc']);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        $i = ($data->{'Delito'} == 'DOLOSO') ? 0 : 1;
                        $column = $header['c'];
                        $row = 8 + $data->{'MES'};
                        $value = $data->{'CANTIDAD'};
                        $this->writeCell($value, $column, $row, $i, $sheet);
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
        $delitosCategorizados = AveMunicipio::join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'AVE_MUNICIPIOS.IDSUBPRO')        
            //->join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
            ->select(
               'sb.SUBPRO',
                DB::raw("
                    CASE
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('1622','1633','1628','1629','1631','1632','1637','1638','1639','1624') THEN 'DOLOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('1625','1626','1627','1623') THEN 'CULPOSO'
                    END AS Delito
                "),
                'AVE_MUNICIPIOS.MES',                
                DB::raw("SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD")
            )
            ->where('AVE_MUNICIPIOS.ANIO', [$year])
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->whereIn('AVE_MUNICIPIOS.IDDELITO', ['1622','1633','1628','1629','1631','1632','1637','1638','1639','1624', '1625','1626','1627','1623'])
            ->groupBy(
                'sb.SUBPRO',
                'AVE_MUNICIPIOS.MES',
                DB::raw("
                    CASE
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('1622','1633','1628','1629','1631','1632','1637','1638','1639','1624') THEN 'DOLOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('1625','1626','1627','1623') THEN 'CULPOSO'
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
