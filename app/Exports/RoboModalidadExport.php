<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RoboModalidadExport implements WithEvents
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
        $templatePath = storage_path('app/templates/9.-Robo_modalidad.xlsm');
        if (file_exists($templatePath)) {
            $headers = [
                0 => [
                    'fisc' => 'APATZINGÁN',
                    'r' => 66
                ],
                1 => [
                    'fisc' => 'LÁZARO CÁRDENAS',
                    'r' => 123
                ],
                2 => [
                    'fisc' => 'MORELIA',
                    'r' => 180
                ],
                3 => [
                    'fisc' => 'URUAPAN',
                    'r' => 237
                ],
                4 => [
                    'fisc' => 'LA PIEDAD',
                    'r' => 294
                ],
                5 => [
                    'fisc' => 'ZAMORA',
                    'r' => 351
                ],
                6 => [
                    'fisc' => 'ZITÁCUARO',
                    'r' => 408
                ],
                7 => [
                    'fisc' => 'COALCOMAN',
                    'r' => 465
                ],
                8 => [
                    'fisc' => 'HUETAMO',
                    'r' => 522
                ],
                9 => [
                    'fisc' => 'JIQUILPAN',
                    'r' => 579
                ]                
            ];
            $delitos = ['ROBO','ROBO_BANCO','CAJA_AHORRO','CASA_HABITACION','COMERCIO','CH_DENTRO_SUC','CH_TARJETA','CH_RETIRO_CAJERO','CH_RETIRO_VENT','ESCUELAS','GASOLINERAS','INDUSTRIA','ATM','RELIGIOSA','INTERIOR_VEHICULO','OFICINAS','PERSONAS_LP','TALLERES','TRANSEUNTE_VIA_PUBLICA','TRANSEUNTE_ABIERTO','TRANSEUNTE','CAMION','COMBI','TAXI','A_VEHICULO','CALIFICADO','CHOFER_AUTOBUS','CHOFER_REPARTIDOR','ARTE_SACRO','AUTOPARTES','COBRE','DOCUMENTOS','EMBARCACIONES','HIDROCARBUROS','MOTOCICLETA','REMOLQUE','TRACTOR','USO','DE_VEHICULO','VEHICULO_MAQUINARIA','CARRETERA','TRANSPORTE_IND','TRANSPORTE_PC','TRANSPORTE_PI','ANT_DESC','CONYUGE','EQUIPARADO','SIMPLE'];
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $periodYear = $year - 3 . ' - ' . $year - 2 . ' - ' . $year - 1 . ' - ' . $year;
            $period = ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] . ' ' . $periodYear : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1] . ' ' . $periodYear;
            $sheet->setCellValue('A4', $period);

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal);

            // Log::info('-------------------RESULTADOS-------------');
            // Log::info($resultados);
            $c = 'c';
            foreach ($headers as $header) {
                $dataFiscalia = $resultados->where('SUBPRO', $header['fisc']);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                       $anios = $year - $data->{'ANIO'};                       
                        if ($anios >= 0 && $anios <= 3) {
                            $row  = $header['r'] + array_search($data->{'Delito'}, $delitos);
                            //Log::info('FISCALIA: ' . $header['fisc'] . ' YEAR: ' . $data->{'ANIO'} . ' ROW: ' . $row);
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
                DB::raw("CASE AVE_MUNICIPIOS.IDDELITO " . 
                    implode(' ', array_map(fn($key, $value) => "WHEN '$key' THEN '$value'", array_keys($delitosCategorias), $delitosCategorias)) . 
                    " END AS Delito"),
                'AVE_MUNICIPIOS.ANIO',
                DB::raw('SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD')
            )
            ->join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
            ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'm.IDSUBPRO2')
            ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 3, $year])
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('DELITOS as d')
                      ->whereColumn('d.IDDELITO', 'AVE_MUNICIPIOS.IDDELITO')
                      ->where('d.ESTATUS', 1);
            })
            ->whereIn('AVE_MUNICIPIOS.IDDELITO', self::ROBOS_LIST)
            ->groupBy('sb.SUBPRO', 'AVE_MUNICIPIOS.ANIO', DB::raw("CASE AVE_MUNICIPIOS.IDDELITO " . 
                implode(' ', array_map(fn($key, $value) => "WHEN '$key' THEN '$value'", array_keys($delitosCategorias), $delitosCategorias)) . 
                " END"))
            ->get();

        // $delitos = AveMunicipio::leftjoin('DELITOS as d', 'AVE_MUNICIPIOS.IDDELITO', '=', 'd.IDDELITO')
        //     ->select(
        //         'sb.SUBPRO',
        //         DB::raw("
        //             	CASE
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1810' THEN 'ROBO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181A' THEN 'ROBO_BANCO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181S' THEN 'CAJA_AHORRO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1819' THEN 'CASA_HABITACION'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181B' THEN 'COMERCIO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182H' THEN 'CH_DENTRO_SUC'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182K' THEN 'CH_TARJETA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182J' THEN 'CH_RETIRO_CAJERO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182I' THEN 'CH_RETIRO_VENT'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181D' THEN 'ESCUELAS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181F' THEN 'GASOLINERAS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181C' THEN 'INDUSTRIA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181W' THEN 'ATM'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181X' THEN 'RELIGIOSA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181Y' THEN 'INTERIOR_VEHICULO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181E' THEN 'OFICINAS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182L' THEN 'PERSONAS_LP'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181G' THEN 'TALLERES'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182A' THEN 'TRANSEUNTE_VIA_PUBLICA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182B' THEN 'TRANSEUNTE_ABIERTO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1818' THEN 'TRANSEUNTE'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181R' THEN 'CAMION'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181Q' THEN 'COMBI'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181P' THEN 'TAXI'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1817' THEN 'A_VEHICULO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1812' THEN 'CALIFICADO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1816' THEN 'CHOFER_AUTOBUS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1815' THEN 'CHOFER_REPARTIDOR'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181N' THEN 'ARTE_SACRO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181M' THEN 'AUTOPARTES'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181L' THEN 'COBRE'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181I' THEN 'DOCUMENTOS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182F' THEN 'EMBARCACIONES'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181O' THEN 'HIDROCARBUROS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181H' THEN 'MOTOCICLETA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181K' THEN 'REMOLQUE'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182G' THEN 'TRACTOR'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '2350' THEN 'USO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1814' THEN 'DE_VEHICULO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181J' THEN 'VEHICULO_MAQUINARIA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181Z' THEN 'CARRETERA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182E' THEN 'TRANSPORTE_IND'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182D' THEN 'TRANSPORTE_PC'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182C' THEN 'TRANSPORTE_PI'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181U' THEN 'ANT_DESC'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181V' THEN 'CONYUGE'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181T' THEN 'EQUIPARADO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1811' THEN 'SIMPLE'
        //                 END AS Delito
        //         "),
        //         'AVE_MUNICIPIOS.ANIO',
        //         DB::raw('SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD')
        //     )
        //     ->join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
        //     ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'm.IDSUBPRO2')
        //     ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 3, $year])
        //     ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
        //     ->whereExists(function ($query) {
        //         $query->select(DB::raw(1))
        //               ->from('DELITOS as d')
        //               ->whereColumn('d.IDDELITO', 'AVE_MUNICIPIOS.IDDELITO')
        //               ->where('d.ESTATUS', 1);
        //     })
        //     ->whereIn('AVE_MUNICIPIOS.IDDELITO', self::ROBOS_LIST)
        //     ->groupBy('sb.SUBPRO', 'AVE_MUNICIPIOS.ANIO', 
        //         DB::raw("
        //             	CASE
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1810' THEN 'ROBO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181A' THEN 'ROBO_BANCO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181S' THEN 'CAJA_AHORRO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1819' THEN 'CASA_HABITACION'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181B' THEN 'COMERCIO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182H' THEN 'CH_DENTRO_SUC'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182K' THEN 'CH_TARJETA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182J' THEN 'CH_RETIRO_CAJERO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182I' THEN 'CH_RETIRO_VENT'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181D' THEN 'ESCUELAS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181F' THEN 'GASOLINERAS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181C' THEN 'INDUSTRIA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181W' THEN 'ATM'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181X' THEN 'RELIGIOSA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181Y' THEN 'INTERIOR_VEHICULO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181E' THEN 'OFICINAS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182L' THEN 'PERSONAS_LP'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181G' THEN 'TALLERES'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182A' THEN 'TRANSEUNTE_VIA_PUBLICA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182B' THEN 'TRANSEUNTE_ABIERTO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1818' THEN 'TRANSEUNTE'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181R' THEN 'CAMION'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181Q' THEN 'COMBI'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181P' THEN 'TAXI'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1817' THEN 'A_VEHICULO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1812' THEN 'CALIFICADO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1816' THEN 'CHOFER_AUTOBUS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1815' THEN 'CHOFER_REPARTIDOR'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181N' THEN 'ARTE_SACRO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181M' THEN 'AUTOPARTES'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181L' THEN 'COBRE'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181I' THEN 'DOCUMENTOS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182F' THEN 'EMBARCACIONES'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181O' THEN 'HIDROCARBUROS'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181H' THEN 'MOTOCICLETA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181K' THEN 'REMOLQUE'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182G' THEN 'TRACTOR'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '2350' THEN 'USO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1814' THEN 'DE_VEHICULO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181J' THEN 'VEHICULO_MAQUINARIA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181Z' THEN 'CARRETERA'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182E' THEN 'TRANSPORTE_IND'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182D' THEN 'TRANSPORTE_PC'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '182C' THEN 'TRANSPORTE_PI'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181U' THEN 'ANT_DESC'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181V' THEN 'CONYUGE'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '181T' THEN 'EQUIPARADO'
        //                     WHEN AVE_MUNICIPIOS.IDDELITO = '1811' THEN 'SIMPLE'
        //                 END
        //         ")
        //     )
        //     ->get();        
                
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
