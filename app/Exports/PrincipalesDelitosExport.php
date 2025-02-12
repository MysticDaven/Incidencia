<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\Anio;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PrincipalesDelitosExport implements WithEvents
{
    use DelitosTrait;
    protected $rangos;
    protected $reporte;
    protected $temporaryFile;    
    protected $meses = [ 1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE' ];
    protected $fiscalias =  ["ESTADO DE MICHOACÁN", "APATZINGÁN", "LÁZARO CÁRDENAS", "MORELIA", "URUAPAN", "LA PIEDAD", "ZAMORA", "ZITÁCUARO","COALCOMAN","HUETAMO","JIQUILPAN"];
    protected $delitos = ['ROBO', 'ROBO DE VEHÍCULO', 'LESIONES DOLOSAS', 'HOMICIDIO CULPOSO', 'NARCOMENUDEO', 'VIOLENCIA FAMILIAR', 'LESIONES CULPOSAS', 'RECEPTACIÓN', 'HOMICIDIO DOLOSO', 'DAÑO CULPOSO', 'DAÑO DOLOSO', 'FRAUDE', 'AMENAZAS', 'DESPOJO', 'VIOLACION', 'ABIGEATO', 'ABUSO SEXUAL', 'PRIVACION DE LA LIBERTAD', 'ABUSO DE AUTORIDAD', 'FALSIFICACION DE DOCUMENTOS', 'ABUSO DE CONFIANZA', 'ALLANAMIENTO DE MORADA', 'OBLIGACION ALIMENTARIA', 'SUSTRACCION DE PERSONA', 'DELITOS ECOLOGIA', 'ARMAS PROHIBIDAS', 'ESTUPRO', 'EXTORSION', 'HOSTIGAMIENTO SEXUAL', 'CONDUCTORES DE VEHICULOS', 'SECUESTRO', 'TRATA DE PERSONAS', 'TURISMO SEXUAL', 'OTRO DELITO'] ;

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

    public function beforeWriting(BeforeWriting $event)
    {
        $headers = [
            0 => [
                'inicio' => 8,
                'h1' => 'A4',
                'h2' => 'A3'
            ],
            1 => [
                'inicio' => 53,
                'h1' => 'A49',
                'h2' => 'A48'
            ],
            2 => [
                'inicio' => 97,
                'h1' => 'A93',
                'h2' => 'A92'
            ],
            3 => [
                'inicio' => 141,
                'h1' => 'A137',
                'h2' => 'A136'
            ],
            4 => [
                'inicio' => 185,
                'h1' => 'A181',
                'h2' => 'A180'
            ],
            5 => [
                'inicio' => 229,
                'h1' => 'A225',
                'h2' => 'A224'
            ],
            6 => [
                'inicio' => 273,
                'h1' => 'A269',
                'h2' => 'A268'
            ],
            7 => [
                'inicio' => 317,
                'h1' => 'A313',
                'h2' => 'A312'
            ],
            8 => [
                'inicio' => 361,
                'h1' => 'A357',
                'h2' => 'A356'
            ],
            9 => [
                'inicio' => 405,
                'h1' => 'A401',
                'h2' => 'A400'
            ],
            10 => [
                'inicio' => 449,
                'h1' => 'A445',
                'h2' => 'A444'
            ],    
        ];

        $templatePath = storage_path('app/templates/PRINCIPALES_DELITOS_ESTATAL.xlsm');

        if (!file_exists($templatePath)) {
            throw new \Exception("La plantilla no existe en la ruta especificada: {$templatePath}");
        }
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $mesInicial = $this->rangos['mes_inicial'];
        $mesFinal = $this->rangos['mes_final'];
        $year = $this->rangos['reporte_anio'];

        // Log::info('MES IN: ' . $mesInicial . ' MES FIN: ' . $mesFinal . ' AÑO: ' . $year);

        $i = 0;
        foreach ($headers as $header) {
            $fisc = $this->fiscalias[$i];
            if ($fisc=="ZITÁCUARO" || $fisc=="MORELIA" || $fisc=="URUAPAN") {
                $msg = 'INCIDENCIA DELICTIVA POR AVERIGUACIÓN PREVIA Y CARPETA DE INVESTIGACIÓN REGISTRADA EN LA FISCALÍA DE ' . $fisc;
            }
            else if ($fisc == 'ESTADO DE MICHOACÁN') {
                $msg = 'INCIDENCIA DELICTIVA POR AVERIGUACIÓN PREVIA Y CARPETA DE INVESTIGACIÓN EN EL ' . $fisc;
            }
            else {
                $msg = 'INCIDENCIA DELICTIVA POR AVERIGAUCIÓN PREVIA REGISTRADA EN LA FISCALÍA DE ' . $fisc;
            }
            $sheet->setCellValue($header['h2'], $msg);
            if ($mesInicial == $mesFinal) {
                $msg = 'COMPARATIVO DE ' . $this->meses[$mesInicial] . ' - ' . $year.' - '.($year+1).' - '.($year+2).' - '.($year+3);
            }
            else {
                $msg = 'COMPARATIVO DE ' . $this->meses[$mesInicial] . ' - ' . $this->meses[$mesFinal] . ' - ' . $year.' - '.($year+1).' - '.($year+2).' - '.($year+3);
            }            
            $h3 = $header['inicio'] - 1;
            $sheet->setCellValue($header['h1'], $msg);
            $sheet->setCellValue('C'.$h3, $year);
            $sheet->setcellValue('D'.$h3, ($year + 1));
            $sheet->setCellValue('E'.$h3, ($year + 2));
            $sheet->setCellValue('F'.$h3, ($year + 3));
            $sheet->SetCellValue("G".$h3, ($year+3).' - '.($year));
            $sheet->SetCellValue("H".$h3, ($year+3).' - '.($year+1));
            $sheet->SetCellValue("I".$h3, ($year+3).' - '.($year+2));
            
            $sheet->SetCellValue("J".$h3, ($year+3).' - '.($year));
            $sheet->SetCellValue("L".$h3, ($year+3).' - '.($year+1));
            $sheet->SetCellValue("N".$h3, ($year+3).' - '.($year+2));
            // if ($header['inicio'] == 53) {
            //     $this->realizarConsulta($year, $mesInicial, $mesFinal, $header['inicio'], $sheet);
            // }     
            $i++;        
        }
        $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal);

        // Log::info('--------RESULTADOS------------');
        // Log::info($resultados);

        // $resultados = response()->json($resultados, 200, [], JSON_UNESCAPED_UNICODE);
        // $datos = json_decode(json_encode($resultados->original, JSON_UNESCAPED_UNICODE), true); // Convertir a array

        // Log::info('RES: ' . json_encode($datos, JSON_UNESCAPED_UNICODE)); 

        // if (isset($datos[0])) {
        //     Log::info('RES apatzingan, creo: ' . json_encode($datos[0], JSON_UNESCAPED_UNICODE)); // Ahora sí mostrará el primer elemento
        //     Log::info($datos[0]['SUBPRO']);
        // }

        // $apatzingan = $datos->where('SUBPRO', 'APATZINGÁN');
        // Log::info('--------APATZINGAN------------');
        // Log::info($apatzingan);

        $h = 0;        
        // array_shift($this->fiscalias);        
        foreach ($this->fiscalias as $fiscalia) {
            $inicio = $headers[$h]['inicio'];
            // Log::info($fiscalia);
            $dataFiscalia = $resultados->where('SUBPRO', $fiscalia)->values()->toArray();
            // Log::info($dataFiscalia);
            $i = 0;
            foreach ($this->delitos as $delito) {
                if ($dataFiscalia[$i]->Delito == $delito) {
                    $value = $dataFiscalia[$i]->{$year};
                    $this->writeCell($value, 0, $inicio, $sheet);
                    $value = $dataFiscalia[$i]->{$year + 1};
                    $this->writeCell($value, 1, $inicio, $sheet);
                    $value = $dataFiscalia[$i]->{$year + 2};
                    $this->writeCell($value, 2, $inicio, $sheet);
                    $value = $dataFiscalia[$i]->{$year + 3};
                    $this->writeCell($value, 3, $inicio, $sheet);
                    $i++;
                    $inicio++;
                }                
                else {
                    $value = 0;
                    $this->writeCell($value, 0, $inicio, $sheet);
                    $this->writeCell($value, 1, $inicio, $sheet);
                    $this->writeCell($value, 2, $inicio, $sheet);
                    $this->writeCell($value, 3, $inicio, $sheet);
                    $inicio++;
                }
            }
            $h++;
        }


        $temporaryFile = $this->temporaryFile->makeLocal();
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($temporaryFile->getLocalPath());

        $event->writer->reopen($temporaryFile, \Maatwebsite\Excel\Excel::XLSX);
        $event->writer->getSheetByIndex(0);
    }

    public function realizarConsulta ($year, $mesInicial, $mesFinal) { //$year, $mesInicial, $mesFinal, $cell, $sheet
        // Log::info('ENTRAS A REALIZAR CONSULTA');
        $delitos = [
            "ROBO" => implode("','", self::ROBOS),
            "ROBO DE VEHÍCULO" => implode("','", self::ROBO_VEHICULO),
            "LESIONES DOLOSAS" => implode("','", self::LESIONES_DOLOSAS),
            "HOMICIDIO CULPOSO" => implode("','", self::HOMICIDIO_CULPOSO),
            "NARCOMENUDEO" => implode("','", self::NARCOMENUDEO),
            "VIOLENCIA FAMILIAR" => implode("','", self::VIOLENCIA_FAMILIAR),
            "LESIONES CULPOSAS" => implode("','", self::LESIONES_CULPOSAS),
            "RECEPTACIÓN" => implode("','", self::RECEPTACION),
            "HOMICIDIO DOLOSO" => implode("','", self::HOMICIDIO_DOLOSO),
            "DAÑO EN LAS COSAS CULPOSO" => implode("','", self::DANIO_CULPOSO),
            "DAÑO EN LAS COSAS DOLOSO" => implode("','", self::DANIO_DOLOSO),
            "FRAUDE" => implode("','", self::FRAUDE),
            "AMENAZAS" => implode("','", self::AMENAZAS),
            "DESPOJO" => implode("','", self::DESPOJO),
            "VIOLACIÓN" => implode("','", self::VIOLACION),
            "ABIGEATO" => implode("','", self::ABIGEATO),
            "ABUSO SEXUAL" => implode("','", self::ABUSO_SEXUAL),
            "PRIVACIÓN DE LA LIBERTAD PERSONAL" =>implode("','",  self::PRIVACION_LIBERTAD),
            "ABUSO DE AUT. Y USO IL. DE LA FUERZA PUB." => implode("','", self::ABUSO_AUTORIDAD),
            "FALSIFICACIÓN Y USO DE DOCUMENTOS FALSOS" => implode("','", self::FALSIFICACION_DOCUMENTOS),
            "ABUSO DE CONFIANZA" => implode("','", self::ABUSO_CONFIANZA),
            "ALLANAMIENTO DE MORADA" => implode("','", self::ALLANAMIENTO_MORADA),
            "INCUMPLIMIENTO DE LA OBLIGACIÓN ALIMENTARIA" => implode("','", self::OBLIGACION_ALIMENTARIA),
            "RETENCIÓN O SUSTRACCIÓN DE PERSONA" => implode("','", self::SUSTRACCION_PERSONA),
            "DELITOS CONTRA LA ECOLOGÍA" => implode("','", self::DELITOS_ECOLOGIA),
            "ARMAS PROHIBIDAS" => implode("','", self::ARMAS_PROHIBIDAS),
            "ESTUPRO" => implode("','", self::ESTUPRO),
            "EXTORSIÓN" => implode("','", self::EXTORSION),
            "HOSTIGAMIENTO SEXUAL" => implode("','", self::HOSTIGAMIENTO_SEXUAL),
            "DEL. COMETIDOS POR CONDUCTORES DE VEHÍCULOS" => implode("','", self::CONDUCTORES_VEHICULOS),
            "SECUESTRO" => implode("','", self::SECUESTRO),
            "TRATA DE PERSONAS" => implode("','", self::TRATA_PERSONAS),
            "TURISMO SEXUAL" => implode("','", self::TURISMO_SEXUAL),
            "RESTO DE DELITOS" => implode("','", self::RESTO_DELITOS) 
        ];        

        // foreach ($delitos as $delito => $ids) {
        //     $sheet->SetCellValue('B'.($cell - 1), $delito);
        //     $auxYear = $year;
        //     $indice = 0;

        //     for ($i = 0; $i <= 3; $i++) {
        //         $cantidad = AveMunicipio::leftJoin('DELITOS', 'AVE_MUNICIPIOS.IDDELITO', '=', 'DELITOS.IDDELITO')
        //             ->join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
        //             ->where('anio', $auxYear)
        //             ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
        //             ->where('m.IDSUBPRO2', 1)
        //             ->where('DELITOS.ESTATUS', 1)
        //             ->whereIn('AVE_MUNICIPIOS.IDDELITO', $ids) // Usamos la variable global
        //             ->sum('CANTIDAD');
        //         Log::info('LA CANTIDAD: ' . $cantidad);
        //         $this->writeCell($cantidad, $indice, $cell, $sheet);
        //         $auxYear++;
        //         //$cell++;
        //         $indice++;
        //     }
        //     $cell++;
        // }

        $delitosCategorizados = AveMunicipio::join('MUNICIPIOS as m', 'm.IDMUNICIPIO', '=', 'AVE_MUNICIPIOS.IDMUNICIPIO')
            ->join('DELITOS as d', 'AVE_MUNICIPIOS.IDDELITO', '=', 'd.IDDELITO')
            ->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'm.IDSUBPRO2')
            ->select(
                'sb.SUBPRO',
                DB::raw("
                    CASE 
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ROBO'] . "') THEN 'ROBO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ROBO DE VEHÍCULO'] . "') THEN 'ROBO DE VEHÍCULO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['LESIONES DOLOSAS'] . "') THEN 'LESIONES DOLOSAS'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['HOMICIDIO CULPOSO'] . "') THEN 'HOMICIDIO CULPOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['NARCOMENUDEO'] . "') THEN 'NARCOMENUDEO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['VIOLENCIA FAMILIAR'] . "') THEN 'VIOLENCIA FAMILIAR'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['LESIONES CULPOSAS'] . "') THEN 'LESIONES CULPOSAS'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['RECEPTACIÓN'] . "') THEN 'RECEPTACIÓN'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['HOMICIDIO DOLOSO'] . "') THEN 'HOMICIDIO DOLOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DAÑO EN LAS COSAS CULPOSO'] . "') THEN 'DAÑO CULPOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DAÑO EN LAS COSAS DOLOSO'] . "') THEN 'DAÑO DOLOSO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['FRAUDE'] . "') THEN 'FRAUDE'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['AMENAZAS'] . "') THEN 'AMENAZAS'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DESPOJO'] . "') THEN 'DESPOJO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['VIOLACIÓN'] . "') THEN 'VIOLACION'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ABIGEATO'] . "') THEN 'ABIGEATO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ABUSO SEXUAL'] . "') THEN 'ABUSO SEXUAL'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['PRIVACIÓN DE LA LIBERTAD PERSONAL'] . "') THEN 'PRIVACION DE LA LIBERTAD'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ABUSO DE AUT. Y USO IL. DE LA FUERZA PUB.'] . "') THEN 'ABUSO DE AUTORIDAD'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['FALSIFICACIÓN Y USO DE DOCUMENTOS FALSOS'] . "') THEN 'FALSIFICACION DE DOCUMENTOS'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ABUSO DE CONFIANZA'] . "') THEN 'ABUSO DE CONFIANZA'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ALLANAMIENTO DE MORADA'] . "') THEN 'ALLANAMIENTO DE MORADA'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['INCUMPLIMIENTO DE LA OBLIGACIÓN ALIMENTARIA'] . "') THEN 'OBLIGACION ALIMENTARIA'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['RETENCIÓN O SUSTRACCIÓN DE PERSONA'] . "') THEN 'SUSTRACCION DE PERSONA'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DELITOS CONTRA LA ECOLOGÍA'] . "') THEN 'DELITOS ECOLOGIA'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ARMAS PROHIBIDAS'] . "') THEN 'ARMAS PROHIBIDAS'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ESTUPRO'] . "') THEN 'ESTUPRO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['EXTORSIÓN'] . "') THEN 'EXTORSION'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['HOSTIGAMIENTO SEXUAL'] . "') THEN 'HOSTIGAMIENTO SEXUAL'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DEL. COMETIDOS POR CONDUCTORES DE VEHÍCULOS'] . "') THEN 'CONDUCTORES DE VEHICULOS'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['SECUESTRO'] . "') THEN 'SECUESTRO'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['TRATA DE PERSONAS'] . "') THEN 'TRATA DE PERSONAS'
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['TURISMO SEXUAL'] . "') THEN 'TURISMO SEXUAL'
                        ELSE 'OTRO DELITO'
                    END AS Delito,
                    CASE
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ROBO'] . "') THEN 1
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ROBO DE VEHÍCULO'] . "') THEN 2
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['LESIONES DOLOSAS'] . "') THEN 3
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['HOMICIDIO CULPOSO'] . "') THEN 4
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['NARCOMENUDEO'] . "') THEN 5
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['VIOLENCIA FAMILIAR'] . "') THEN 6
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['LESIONES CULPOSAS'] . "') THEN 7
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['RECEPTACIÓN'] . "') THEN 8
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['HOMICIDIO DOLOSO'] . "') THEN 9
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DAÑO EN LAS COSAS CULPOSO'] . "') THEN 10
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DAÑO EN LAS COSAS DOLOSO'] . "') THEN 11
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['FRAUDE'] . "') THEN 12
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['AMENAZAS'] . "') THEN 13
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DESPOJO'] . "') THEN 14
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['VIOLACIÓN'] . "') THEN 15
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ABIGEATO'] . "') THEN 16
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ABUSO SEXUAL'] . "') THEN 17
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['PRIVACIÓN DE LA LIBERTAD PERSONAL'] . "') THEN 18
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ABUSO DE AUT. Y USO IL. DE LA FUERZA PUB.'] . "') THEN 19
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['FALSIFICACIÓN Y USO DE DOCUMENTOS FALSOS'] . "') THEN 20
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ABUSO DE CONFIANZA'] . "') THEN 21
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ALLANAMIENTO DE MORADA'] . "') THEN 22
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['INCUMPLIMIENTO DE LA OBLIGACIÓN ALIMENTARIA'] . "') THEN 23
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['RETENCIÓN O SUSTRACCIÓN DE PERSONA'] . "') THEN 24
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DELITOS CONTRA LA ECOLOGÍA'] . "') THEN 25
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ARMAS PROHIBIDAS'] . "') THEN 26
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['ESTUPRO'] . "') THEN 27
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['EXTORSIÓN'] . "') THEN 28
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['HOSTIGAMIENTO SEXUAL'] . "') THEN 29
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['DEL. COMETIDOS POR CONDUCTORES DE VEHÍCULOS'] . "') THEN 30
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['SECUESTRO'] . "') THEN 31
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['TRATA DE PERSONAS'] . "') THEN 32
                        WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $delitos['TURISMO SEXUAL'] . "') THEN 33
                        ELSE 34
                    END AS OrdenDelito
                "),
                'AVE_MUNICIPIOS.ANIO',
                'AVE_MUNICIPIOS.CANTIDAD'
            )        
            ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year, $year+3])
            ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
            ->where('d.ESTATUS', 1);
                
        // $resultados = DB::query()
        //     ->fromSub($delitosCategorizados, 'DelitosCategorizados')
        //     ->select(
        //         'SUBPRO',
        //         'Delito',
        //         DB::raw("SUM(CASE WHEN ANIO = 2018 THEN CANTIDAD ELSE 0 END) AS [2018]"),
        //         DB::raw("SUM(CASE WHEN ANIO = 2019 THEN CANTIDAD ELSE 0 END) AS [2019]"),
        //         DB::raw("SUM(CASE WHEN ANIO = 2020 THEN CANTIDAD ELSE 0 END) AS [2020]"),
        //         DB::raw("SUM(CASE WHEN ANIO = 2021 THEN CANTIDAD ELSE 0 END) AS [2021]")
        //     )
        //     ->groupBy('SUBPRO', 'Delito', 'OrdenDelito')
        //     ->orderBy('SUBPRO')
        //     //->orderBy('Delito')
        //     ->orderBy('OrdenDelito')
        //     ->get();  
            
        //---------------------------------------------------------------------------------------------------------------------------
        $resultados = DB::query()
        ->fromSub($delitosCategorizados, 'DelitosCategorizados')
        ->select(
            'SUBPRO',
            'Delito',
            DB::raw("SUM(CASE WHEN ANIO = " . $year . " THEN CANTIDAD ELSE 0 END) AS [" . $year . "]"),
            DB::raw("SUM(CASE WHEN ANIO = " . ($year + 1) . " THEN CANTIDAD ELSE 0 END) AS [" . ($year + 1) . "]"),
            DB::raw("SUM(CASE WHEN ANIO = " . ($year + 2) . " THEN CANTIDAD ELSE 0 END) AS [" . ($year + 2) . "]"),
            DB::raw("SUM(CASE WHEN ANIO = " . ($year + 3) . " THEN CANTIDAD ELSE 0 END) AS [" . ($year + 3) . "]"),
            'OrdenDelito'
        )
        ->groupBy('SUBPRO', 'Delito', 'OrdenDelito');    

        // Agregar la fila "ESTADO" con la suma total de cada delito
        $estado = DB::query()
            ->fromSub($delitosCategorizados, 'DelitosCategorizados')
            ->select(
                DB::raw("'ESTADO DE MICHOACÁN' AS SUBPRO"),
                'Delito',
                DB::raw("SUM(CASE WHEN ANIO = " . $year . " THEN CANTIDAD ELSE 0 END) AS [" . $year . "]"),
                DB::raw("SUM(CASE WHEN ANIO = " . ($year + 1) . " THEN CANTIDAD ELSE 0 END) AS [" . ($year + 1) . "]"),
                DB::raw("SUM(CASE WHEN ANIO = " . ($year + 2) . " THEN CANTIDAD ELSE 0 END) AS [" . ($year + 2) . "]"),
                DB::raw("SUM(CASE WHEN ANIO = " . ($year + 3) . " THEN CANTIDAD ELSE 0 END) AS [" . ($year + 3) . "]"),
                'OrdenDelito'
            )
            ->groupBy('Delito', 'OrdenDelito');

        // Combinar ambos resultados con UNION ALL
        $resultadosFinales = DB::query()
            ->fromSub(
                $estado->unionAll($resultados),
                'Final'
            )
            ->orderBy('SUBPRO')
            ->orderBy('OrdenDelito')
            ->get();


        return $resultadosFinales;
        // return response()->json($resultados, 200, [], JSON_UNESCAPED_UNICODE);
        // return $resultados->toArray();
                    
    }

    public static function writeCell ($value, $indice, $cell, $sheet) {
        // Log::info('ENTRAS A WRITE');
        $columns = ['C', 'D', 'E', 'F'];
            // Log::info('ESCRIBES DE VERDAD');
            $auxCell = $columns[$indice] . $cell;
            $sheet->SetCellValue($auxCell, $value);
    }
}