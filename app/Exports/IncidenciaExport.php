<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IncidenciaExport implements WithEvents
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
        $templatePath = storage_path('app/templates/4.INCIDENCIA ESTATAL_FISCALIA.xlsm');
        if (file_exists($templatePath)) {
            $headers = [
                1 => 53,
                2 => 97,
                3 => 141,
                4 => 185,
                8 => 229,
                5 => 273,
                6 => 317,
                9 => 361,
                10 => 405,
                11 => 449
            ];
            $delitos = ['ROBO', 'ROBO DE VEHÍCULO', 'LESIONES DOLOSAS', 'HOMICIDIO CULPOSO', 'NARCOMENUDEO', 'VIOLENCIA FAMILIAR', 'LESIONES CULPOSAS', 'RECEPTACIÓN', 'HOMICIDIO DOLOSO', 'DAÑO CULPOSO', 'DAÑO DOLOSO', 'FRAUDE', 'AMENAZAS', 'DESPOJO', 'VIOLACIÓN', 'ABIGEATO', 'ABUSO SEXUAL', 'PRIVACION DE LA LIBERTAD', 'ABUSO DE AUTORIDAD', 'FALSIFICACION DE DOCUMENTOS', 'ABUSO DE CONFIANZA', 'ALLANAMIENTO DE MORADA', 'OBLIGACION ALIMENTARIA', 'SUSTRACCION DE PERSONA', 'DELITOS ECOLOGIA', 'ARMAS PROHIBIDAS', 'ESTUPRO', 'EXTORSION', 'HOSTIGAMIENTO SEXUAL', 'CONDUCTORES DE VEHICULOS', 'SECUESTRO', 'TRATA DE PERSONAS', 'TURISMO SEXUAL', 'OTRO DELITO'];              

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $period = ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];
            $period = $period . ' ' . $year - 1 . ' y ' . $period . ' ' . $year;

            $sheet->setCellValue('A4', $period);
            $sheet->setCellValue('C7', $year - 1);
            $sheet->setCellValue('P7', $year);
            $sheet->setCellValue('AG1', 1);
            $sheet->setCellValue('AC7', 'TOTAL ' . $year - 1);
            $sheet->setCellValue('AD7', 'TOTAL ' . $year);

            $sheet->getPageSetup()->setPrintArea('A1:AE483');

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal);
            // Log::info('---------------RESULTADOS FISCALIAS----------------');
            // Log::info($resultados);

            foreach ($headers as $header => $fila) {
                $dataFiscalia = $resultados->where('IDSUBPRO', $header);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        $c = ($data->{'ANIO'} == $year)  ? 'o' : 'b';
                        $row = array_search($data->{'Delito'}, $delitos) + $fila;
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
            'AMENAZAS' => self::AMENAZAS_V2,
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
            'SECUESTRO' => self::SECUESTRO_V2,
            'TRATA DE PERSONAS' => self::TRATA_PERSONAS,
            'TURISMO SEXUAL' => self::TURISMO_SEXUAL
        ];        
             
        $caseDelitos = "CASE ";
        foreach ($delitosCategorias as $categoria => $id) {
            $caseDelitos .= "WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . implode("','", $id) . "') THEN '$categoria' ";
        }
        $caseDelitos .= "ELSE 'OTRO DELITO' END";

        $delitos = AveMunicipio::leftJoin('DELITOS as d', 'AVE_MUNICIPIOS.IDDELITO', '=', 'd.IDDELITO')
        //->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'AVE_MUNICIPIOS.IDSUBPRO')
        ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 1, $year])
        ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('DELITOS AS d')
                ->whereColumn('d.IDDELITO', 'AVE_MUNICIPIOS.IDDELITO')
                ->where('d.ESTATUS', 1);
        })
        ->selectRaw("
            AVE_MUNICIPIOS.IDSUBPRO,
            $caseDelitos AS Delito,
            AVE_MUNICIPIOS.MES,
            AVE_MUNICIPIOS.ANIO,
            SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD    
        ")
        ->groupBy('AVE_MUNICIPIOS.IDSUBPRO', 'AVE_MUNICIPIOS.ANIO', 'AVE_MUNICIPIOS.MES',
            DB::raw("$caseDelitos"))
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
