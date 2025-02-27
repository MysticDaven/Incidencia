<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RoboModalidadMesExport implements WithEvents
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
        $templatePath = storage_path('app/templates/9.-Robo_meses.xlsm');
        if (file_exists($templatePath)) {
            $headers = [
                0 => [
                    'fisc' => 'APATZINGÁN',
                    'r' => 65
                ],
                1 => [
                    'fisc' => 'LÁZARO CÁRDENAS',
                    'r' => 123
                ],
                2 => [
                    'fisc' => 'MORELIA',
                    'r' => 181
                ],
                3 => [
                    'fisc' => 'URUAPAN',
                    'r' => 239
                ],
                4 => [
                    'fisc' => 'LA PIEDAD',
                    'r' => 297
                ],
                5 => [
                    'fisc' => 'ZAMORA',
                    'r' => 355
                ],
                6 => [
                    'fisc' => 'ZITÁCUARO',
                    'r' => 413
                ],
                7 => [
                    'fisc' => 'COALCOMAN',
                    'r' => 471
                ],
                8 => [
                    'fisc' => 'HUETAMO',
                    'r' => 529
                ],
                9 => [
                    'fisc' => 'JIQUILPAN',
                    'r' => 587
                ]                
            ];
            $delitos = ['ROBO','ROBO_BANCO','CAJA_AHORRO','CASA_HABITACION','COMERCIO','CH_DENTRO_SUC','CH_TARJETA','CH_RETIRO_CAJERO','CH_RETIRO_VENT','ESCUELAS','GASOLINERAS','INDUSTRIA','ATM','RELIGIOSA','INTERIOR_VEHICULO','OFICINAS','PERSONAS_LP','TALLERES','TRANSEUNTE_VIA_PUBLICA','TRANSEUNTE_ABIERTO','TRANSEUNTE','CAMION','COMBI','TAXI','A_VEHICULO','CALIFICADO','CHOFER_AUTOBUS','CHOFER_REPARTIDOR','ARTE_SACRO','AUTOPARTES','COBRE','DOCUMENTOS','EMBARCACIONES','HIDROCARBUROS','MOTOCICLETA','REMOLQUE','TRACTOR','USO','DE_VEHICULO','VEHICULO_MAQUINARIA','CARRETERA','TRANSPORTE_IND','TRANSPORTE_PC','TRANSPORTE_PI','ANT_DESC','CONYUGE','EQUIPARADO','SIMPLE'];            
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $period = ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];
            $period = $period . ' ' . $year;

            $sheet->setCellValue('A4', $period);

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal);
            Log::info('-------------------RESULTADOS-------------');
            Log::info($resultados);

            $c = 'b';
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

    public function realizarConsulta ($year, $mesInicial, $mesFinal) {
        $delitosCategorias = [
            '1810' => 'ROBO',
            '181A' => 'ROBO_BANCO',
            '181S' => 'CAJA_AHORRO',
            '1819' => 'CASA_HABITACION',
            '181B' => 'COMERCIO',
            '182H' => 'CH_DENTRO_SUC',
            '182K' => 'CH_TARJETA',
            '182J' => 'CH_RETIRO_CAJERO',
            '182I' => 'CH_RETIRO_VENT',
            '181D' => 'ESCUELAS',
            '181F' => 'GASOLINERAS',
            '181C' => 'INDUSTRIA',
            '181W' => 'ATM',
            '181X' => 'RELIGIOSA',
            '181Y' => 'INTERIOR_VEHICULO',
            '181E' => 'OFICINAS',
            '182L' => 'PERSONAS_LP',
            '181G' => 'TALLERES',
            '182A' => 'TRANSEUNTE_VIA_PUBLICA',
            '182B' => 'TRANSEUNTE_ABIERTO',
            '1818' => 'TRANSEUNTE',
            '181R' => 'CAMION',
            '181Q' => 'COMBI',
            '181P' => 'TAXI',
            '1817' => 'A_VEHICULO',
            '1812' => 'CALIFICADO',
            '1816' => 'CHOFER_AUTOBUS',
            '1815' => 'CHOFER_REPARTIDOR',
            '181N' => 'ARTE_SACRO',
            '181M' => 'AUTOPARTES',
            '181L' => 'COBRE',
            '181I' => 'DOCUMENTOS',
            '182F' => 'EMBARCACIONES',
            '181O' => 'HIDROCARBUROS',
            '181H' => 'MOTOCICLETA',
            '181K' => 'REMOLQUE',
            '182G' => 'TRACTOR',
            '2350' => 'USO',
            '1814' => 'DE_VEHICULO',
            '181J' => 'VEHICULO_MAQUINARIA',
            '181Z' => 'CARRETERA',
            '182E' => 'TRANSPORTE_IND',
            '182D' => 'TRANSPORTE_PC',
            '182C' => 'TRANSPORTE_PI',
            '181U' => 'ANT_DESC',
            '181V' => 'CONYUGE',
            '181T' => 'EQUIPARADO',
            '1811' => 'SIMPLE',
        ];

        $delitos = AveMunicipio::leftjoin('DELITOS as d', 'AVE_MUNICIPIOS.IDDELITO', '=', 'd.IDDELITO')
            ->select(
                'sb.SUBPRO',
                DB::raw("
                    CASE AVE_MUNICIPIOS.IDDELITO " .
                    implode(' ', array_map(fn($key, $value) => "WHEN '$key' THEN '$value'", array_keys($delitosCategorias), $delitosCategorias)) .
                    " END AS Delito"
                ),
                'AVE_MUNICIPIOS.MES',
                DB::raw('SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD')
            )
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
            ->whereIn('AVE_MUNICIPIOS.IDDELITO', self::ROBOS_LIST)
            ->groupBy(
                'sb.SUBPRO',
                'AVE_MUNICIPIOS.MES',
                DB::raw(
                    "CASE AVE_MUNICIPIOS.IDDELITO " .
                    implode(' ', array_map(fn($key, $value) => "WHEN '$key' THEN '$value'", array_keys($delitosCategorias), $delitosCategorias)) . 
                    " END"
                )
            )
            ->get();

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
