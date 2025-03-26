<?php

namespace App\Exports;

use App\DelitosTrait;
use App\Models\AveMunicipio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DelitosModalidadExport implements WithEvents
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
        $templatePath = storage_path('app/templates/13.-delitos_modalidad.xlsm'); //13.-delitos_modalidad.xlsm');   4.INCIDENCIA ESTATAL_FISCALIA.xlsm');
        if (file_exists($templatePath)) {
            $headers = [
                1 => 'C',
                2 => 'L',
                3 => 'U',
                4 => 'AD',
                8 => 'AM',
                5 => 'AV',
                6 => 'BE',
                9 => 'BN',
                10 => 'BW',
                11 => 'CF'
            ];
            $delitos = ['ABANDONO', 'ABIGEATO', 'ABIGEATO_TENT', 'ABORTO_CON', 'ABORTO_TENT', 'ABORTO_ESP', 'ABORTO_SCON', 'ABORTO_VOL', 'ABUSO_PUB', 'ABUSO_SCON', 'ABUSO_EQU', 'ABUSO_ESP', 'ABUSO_SEX16', 'ABUSO_SEX18', 'ABUSOS_SEX', 'ACECHANZA', 'ACOSO_SEX', 'ALLANAMIENTO_MER', 'ALLANAMIENTO_MOR', 'ALLANAMIENTO_TEN', 'ALTERACION_CIV', 'ALTERAR_DOC', 'AMENAZA_SEC', 'AMENAZAS', 'ARMAS_PRO', 'ASALTO', 'ASALTO_B_PAR', 'ASALTO_B_CAR', 'ASALTO_C_PAR', 'ASALTO_C_CAR', 'ASALTO_C_PUB', 'ASALTO_CF_PAR', 'ASALTO_CF_CAR', 'ASALTO_CF_PUB', 'ASALTO_TEN', 'ATAQUES_INT', 'ATAQUES_IMA', 'ATAQUES_VEH', 'ATAQUES_TRA', 'ATAQUES_HON', 'ATENTADO_FAL', 'BIGAMIA', 'CALUMNIAS', 'COALICIÓN_PÚB', 'COHECHO', 'CONCUSION', 'CONSPIRACION', 'CORRUPCION_EDA', 'CORRUPCION_EMP', 'CRUELDAD_ANI', 'CRUELDAD_MAL', 'DAÑO_URB', 'DAÑO_CUL', 'DAÑO_VEH', 'DAÑO_DOL', 'DAÑO_IMP', 'DAÑO_EXP', 'DAÑO_PRE', 'DAÑO_SAL', 'TORTURA', 'PATRONES', 'DELINCUENCIA_ORG', 'DELITO_JUS', 'DELITO_POL', 'DELITO_PUB', 'DELITO_CV', 'DELITO_EBR', 'DELITO_SP', 'DELITO_AMB', 'DELITO_URB', 'DELITO_CAL', 'DELITO_LIT', 'DELITO_SPUB', 'DELITO_HAL', 'DELITO_PS', 'DELITO_ELE', 'DELITO_FOR', 'DELITO_FE', 'DENEGACION', 'DESAPARICION_PAR', 'DESAPARICION_PER', 'DESOBEDIENCIA', 'DESOBEDIENCIA_PAR', 'DESPOJO', 'DESPOJO_TEN', 'DIFAMACION', 'DIFUSIÓN_INF', 'DISCRIMINACION', 'EJERCICIO_FUN', 'EJERCICIO_DER', 'EJERCICIO_LAB', 'EJERCICIO_PUB', 'ELABORACION_PLA', 'ENCUBRIMIENTO', 'ENCUBRIMIENTO_REC', 'ENRIQUECIMIENTO', 'ESTUPRO', 'EVASION', 'EVASION_TEN', 'EXTORSION', 'EXTORSION_TEN', 'EXTORSION_TEL', 'EXTORSION_TELT', 'FALSEDAD_AUT', 'FALSIFICACION_FAL', 'FALSIFICACION_SIM', 'FALSIFICACION_DOC', 'FALSIFICACION_EQU', 'FALSIFICACIÓN_UNI', 'FEMINICIDIO', 'FEMINICIDIO_TEN', 'FILICIDIO', 'FRAUDE', 'FRAUDE_TEN', 'FRAUDE_EQU', 'FRAUDE_ESP', 'FRAUDE_S', 'HECHOS', 'HOMICIDIO_CAL', 'HOMICIDIO_FUE', 'HOMICIDIO_CUL', 'HOMICIDIO_BLA', 'HOMICIDIO_C_FUE', 'HOMICIDIO_EDA', 'HOMICIDIO_DOL', 'HOMICIDIO_D_BLA', 'HOMICIDIO_D_FUE', 'HOMICIDIO_D_EDA', 'HOMICIDIO_PAR', 'HOMICIDIO_TEN', 'HOMICIDIO_SEX', 'HOMICIDIO_REL', 'HOMICIDIO_R', 'HOMICIDIO_VEH', 'HOMICIDIO_SUI', 'HOMICIDIO_PRE', 'HOMICIDIO_SIM', 'HOSTIGAMIENTO', 'IMPUTACION', 'INCESTO', 'INCESTO_TEN', 'INCUMPLIMIENTO', 'INFIDELIDAD _CUS', 'INHUMACION_IND', 'INSOLVENCIA_FRA', 'INSOLVENCIA_SIM', 'INSTIGACION_SUI', 'INSTIGACION_DEL', 'INTIMIDACION', 'LENOCINIO', 'LENOCINIO_PER', 'LESIONES_CAL', 'LESIONES_CUL', 'LESIONES_BLA', 'LESIONES_FUE', 'LESIONES_DOL', 'LESIONES_EDA', 'LESIONES_D_BLA', 'LESIONES_D_FUE', 'LESIONES_GEN', 'LESIONES_SEX', 'LESIONES_TEN', 'LESIONES_PAR', 'LESIONES_R', 'LESIONES_IMP', 'LESIONES_PRE', 'MALTRATO_ANI', 'MOTIN', 'NARCOMENUDEO', 'NEGACION_MED', 'NEGACION_PUB', 'OMISION', 'OMISION_AUX', 'OMISION_CUI', 'OMISION_FOR', 'OMISION_EQU', 'OPERACIONES_ILI', 'OPOSICION', 'OTRO_SEC', 'PARRICIDIO', 'PECULADO', 'PELIGRO', 'PORNOGRAFIA_EDA', 'PORNOGRAFIA_TUR', 'PRACTICA_MED', 'PREVARICACION', 'PRIVACION_PER', 'PRIVACION_TEN', 'QUEBRANTAMIENTO', 'RAPTO EN GRADO DE TENTATIVA', 'REBELION', 'RECEPTACION', 'RESISTENCIA', 'RESPONSABILIDAD_TEC', 'RETENCION_EDA', 'RETENCION_TEN', 'REVELACION', 'ROBO_B', 'ROBO_AHO', 'ROBO_HAB', 'ROBO_AUT', 'ROBO_COM', 'ROBO_CH', 'ROBO_CHT', 'ROBO_ATM', 'ROBO_VEN', 'ROBO_ESC', 'ROBO_GAS', 'ROBO_IND', 'ROBO_IB', 'ROBO_REL', 'ROBO_VEH', 'ROBO_OFI', 'ROBO_PRI', 'ROBO_TALL', 'ROBO_PUB', 'ROBO_VPUB', 'ROBO_T', 'ROBO_TP', 'ROBO_TPC', 'ROBO_TPT', 'ROBO_TRA', 'ROBO_V', 'ROBO_CAL', 'ROBO_AS', 'ROBO_AUTP', 'ROBO_COB', 'ROBO_DOC', 'ROBO_HID', 'ROBO_MOT', 'ROBO_REM', 'ROBO_TRAC', 'ROBO_USO', 'ROBO_DV', 'ROBO_MAQ', 'ROBO_CAR', 'ROBO_GTEN', 'ROBO_TRAI', 'ROBO_COL', 'ROBO_PIND', 'ROBO_DES', 'ROBO_CY', 'ROBO_EQU', 'ROBO_SIM', 'SABOTAJE', 'SECUESTRO', 'SECUESTRO_REH', 'SECUESTRO_TEN', 'SECUESTRO_EXP', 'SECUESTRO_EXT', 'SECUESTRO_D', 'SECUESTRO_SIM', 'SECUESTRO_SP', 'SEDICION', 'SIMULACION_DOC', 'SIMULACION_P', 'SUMINISTRO_MED', 'TPE', 'TERRORISMO', 'TERRORISMO_GT', 'TRAFICO_INF', 'TRAFICO_EDA', 'TRATA', 'ULTRAJE', 'ULTRAJE_MP', 'USO_FAC', 'USO_EME', 'USO_EST', 'USURA', 'USURPACION_SCON', 'USURPACION_PRO', 'VAGANCIA', 'VARIACION_DP', 'VIOLACION', 'VIOLACION_PRI', 'VIOLACION_CORR', 'VIOLACION_S', 'VIOLACION_PAR', 'VIOLACION_TEN', 'VIOLACION_EQU', 'VIOLENCIA_SEX', 'VIOLENCIA_FAM', 'VIOLENCIA_MUJ', 'VIOLENCIA_VIC'];

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $mesInicial = $this->rangos['mes_inicial'];
            $mesFinal = $this->rangos['mes_final'];
            $year = $this->rangos['reporte_anio'];

            $period = ($mesInicial == $mesFinal) ? self::MONTH[$mesInicial - 1] : self::MONTH[$mesInicial - 1] . ' - ' . self::MONTH[$mesFinal - 1];
            $period .= ' ' . $year - 2 . ' - ' . $year;

            $sheet->setCellValue('C7', $period);
            $sheet->setCellValue('C10', $year - 2);
            $sheet->setCellValue('D10', $year - 1);
            $sheet->setCellValue('E10', $year);

            $resultados = $this->realizarConsulta($year, $mesInicial, $mesFinal);
            // Log::info('---------------RESULTADOS FISCALIAS----------------');
            // Log::info($resultados);
            $fila = 11;
            foreach ($headers as $header => $columna) {
                $dataFiscalia = $resultados->where('IDSUBPRO', $header);
                if (count($dataFiscalia) > 0) {
                    foreach ($dataFiscalia as $data) {
                        if ($data->{'Delito'} != 'OTRO DELITO') {
                            $anios = $year - $data->{'ANIO'};
                            if ($anios >= 0 && $anios <= 2) {
                                $row = array_search($data->{'Delito'}, $delitos) + $fila;
                                $value = $data->{'CANTIDAD'};
                                $this->writeCell($value, $columna, $row, self::I[$anios], $sheet);
                            }
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
            'ABANDONO' => '2630',
            'ABIGEATO' => '1830',
            'ABIGEATO_TENT' => '1831',
            'ABORTO_CON' => '1662',
            'ABORTO_TENT' => '1661',
            'ABORTO_ESP' => '1664',
            'ABORTO_SCON' => '1663',
            'ABORTO_VOL' => '1660',
            'ABUSO_PUB' => '0770',
            'ABUSO_SCON' => '1840',
            'ABUSO_EQU' => '1842',
            'ABUSO_ESP' => '1841',
            'ABUSO_SEX16' => '1432',
            'ABUSO_SEX18' => '1434',
            'ABUSOS_SEX' => '1430',
            'ACECHANZA' => '2313',
            'ACOSO_SEX' => '1433',
            'ALLANAMIENTO_MER' => '1372',
            'ALLANAMIENTO_MOR' => '1370',
            'ALLANAMIENTO_TEN' => '1371',
            'ALTERACION_CIV' => '1110',
            'ALTERAR_DOC' => '1835',
            'AMENAZA_SEC' => '1341',
            'AMENAZAS' => '1340',
            'ARMAS_PRO' => '0230',
            'ASALTO' => '1360',
            'ASALTO_B_PAR' => '1363',
            'ASALTO_B_CAR' => '1369',
            'ASALTO_C_PAR' => '1364',
            'ASALTO_C_CAR' => '136A',
            'ASALTO_C_PUB' => '1367',
            'ASALTO_CF_PAR' => '1362',
            'ASALTO_CF_CAR' => '1368',
            'ASALTO_CF_PUB' => '1365',
            'ASALTO_TEN' => '1361',
            'ATAQUES_INT' => '1560',
            'ATAQUES_IMA' => '1570',
            'ATAQUES_VEH' => '2560',
            'ATAQUES_TRA' => '0310',
            'ATAQUES_HON' => '1550',
            'ATENTADO_FAL' => '2340',
            'BIGAMIA' => '1120',
            'CALUMNIAS' => '1530',
            'COALICIÓN_PÚB' => '0750',
            'COHECHO' => '0710',
            'CONCUSION' => '0730',
            'CONSPIRACION' => '0140',
            'CORRUPCION_EDA' => '0520',
            'CORRUPCION_EMP' => '0521',
            'CRUELDAD_ANI' => '2151',
            'CRUELDAD_MAL' => '2150',
            'DAÑO_URB' => '1886',
            'DAÑO_CUL' => '1883',
            'DAÑO_VEH' => '1885',
            'DAÑO_DOL' => '1882',
            'DAÑO_IMP' => '1894',
            'DAÑO_EXP' => '1884',
            'DAÑO_PRE' => '1881',
            'DAÑO_SAL' => '2312',
            'TORTURA' => '0790',
            'PATRONES' => '1910',
            'DELINCUENCIA_ORG' => '0240',
            'DELITO_JUS' => '0870',
            'DELITO_POL' => '0150',
            'DELITO_PUB' => '0440',
            'DELITO_CV' => '0320',
            'DELITO_EBR' => '0321',
            'DELITO_SP' => '0742',
            'DELITO_AMB' => '2110',
            'DELITO_URB' => '2120',
            'DELITO_CAL' => '2121',
            'DELITO_LIT' => '0810',
            'DELITO_SPUB' => '0260',
            'DELITO_HAL' => '0261',
            'DELITO_PS' => '1020',
            'DELITO_ELE' => '2010',
            'DELITO_FOR' => '2112',
            'DELITO_FE' => '2113',
            'DENEGACION' => '2430',
            'DESAPARICION_PAR' => '1313',
            'DESAPARICION_PER' => '1312',
            'DESOBEDIENCIA' => '0411',
            'DESOBEDIENCIA_PAR' => '0410',
            'DESPOJO' => '1870',
            'DESPOJO_TEN' => '1871',
            'DIFAMACION' => '1520X',
            'DIFUSIÓN_INF' => '0267',
            'DISCRIMINACION' => '1540',
            'EJERCICIO_FUN' => '2635',
            'EJERCICIO_DER' => '0860',
            'EJERCICIO_LAB' => '2540',
            'EJERCICIO_PUB' => '0740',
            'ELABORACION_PLA' => '2580',
            'ENCUBRIMIENTO' => '0850',
            'ENCUBRIMIENTO_REC' => '2370',
            'ENRIQUECIMIENTO' => '0780',
            'ESTUPRO' => '1420',
            'EVASION' => '0210',
            'EVASION_TEN' => '0211',
            'EXTORSION' => '1350',
            'EXTORSION_TEN' => '1351',
            'EXTORSION_TEL' => '1352',
            'EXTORSION_TELT' => '1353',
            'FALSEDAD_AUT' => '0840',
            'FALSIFICACION_FAL' => '0920',
            'FALSIFICACION_SIM' => '0910',
            'FALSIFICACION_DOC' => '0922',
            'FALSIFICACION_EQU' => '0923',
            'FALSIFICACIÓN_UNI' => '0960',
            'FEMINICIDIO' => '161G',
            'FEMINICIDIO_TEN' => '161M',
            'FILICIDIO' => '1669',
            'FRAUDE' => '1850',
            'FRAUDE_TEN' => '1851',
            'FRAUDE_EQU' => '1855',
            'FRAUDE_ESP' => '1853',
            'FRAUDE_S' => '1854',
            'HECHOS' => '9999',
            'HOMICIDIO_CAL' => '1617',
            'HOMICIDIO_FUE' => '161K',
            'HOMICIDIO_CUL' => '1613',
            'HOMICIDIO_BLA' => '161B',
            'HOMICIDIO_C_FUE' => '161A',
            'HOMICIDIO_EDA' => '161J',
            'HOMICIDIO_DOL' => '1614',
            'HOMICIDIO_D_BLA' => '161D',
            'HOMICIDIO_D_FUE' => '161C',
            'HOMICIDIO_D_EDA' => '161I',
            'HOMICIDIO_PAR' => '1619',
            'HOMICIDIO_TEN' => '1611',
            'HOMICIDIO_SEX' => '161H',
            'HOMICIDIO_REL' => '1671',
            'HOMICIDIO_R' => '1618',
            'HOMICIDIO_VEH' => '1615',
            'HOMICIDIO_SUI' => '161E',
            'HOMICIDIO_PRE' => '1612',
            'HOMICIDIO_SIM' => '1616',
            'HOSTIGAMIENTO' => '1431',
            'IMPUTACION' => '0830',
            'INCESTO' => '1130',
            'INCESTO_TEN' => '1131',
            'INCUMPLIMIENTO' => '1140',
            'INFIDELIDAD _CUS' => '0760',
            'INHUMACION_IND' => '1210',
            'INSOLVENCIA_FRA' => '2360',
            'INSOLVENCIA_SIM' => '2330',
            'INSTIGACION_SUI' => '1630',
            'INSTIGACION_DEL' => '0540',
            'INTIMIDACION' => '2390',
            'LENOCINIO' => '0531',
            'LENOCINIO_PER' => '0530',
            'LESIONES_CAL' => '1639',
            'LESIONES_CUL' => '1623',
            'LESIONES_BLA' => '1627',
            'LESIONES_FUE' => '1626',
            'LESIONES_DOL' => '1624',
            'LESIONES_EDA' => '1633',
            'LESIONES_D_BLA' => '1629',
            'LESIONES_D_FUE' => '1628',
            'LESIONES_GEN' => '1631',
            'LESIONES_SEX' => '1632',
            'LESIONES_TEN' => '1621',
            'LESIONES_PAR' => '1637',
            'LESIONES_R' => '1638',
            'LESIONES_IMP' => '1625',
            'LESIONES_PRE' => '1622',
            'MALTRATO_ANI' => '2152',
            'MOTIN' => '0130',
            'NARCOMENUDEO' => '2311',
            'NEGACION_MED' => '2530',
            'NEGACION_PUB' => '2410',
            'OMISION' => '1740',
            'OMISION_AUX' => '1730',
            'OMISION_CUI' => '1720',
            'OMISION_FOR' => '2470',
            'OMISION_EQU' => '2480',
            'OPERACIONES_ILI' => '1890',
            'OPOSICION' => '0420',
            'OTRO_SEC' => '1327',
            'PARRICIDIO' => '1640',
            'PECULADO' => '0720',
            'PELIGRO' => '1750',
            'PORNOGRAFIA_EDA' => '0551',
            'PORNOGRAFIA_TUR' => '0550',
            'PRACTICA_MED' => '2610',
            'PREVARICACION' => '2440',
            'PRIVACION_PER' => '1310',
            'PRIVACION_TEN' => '1311',
            'QUEBRANTAMIENTO' => '0220',
            'RAPTO EN GRADO DE TENTATIVA' => '1331',
            'REBELION' => '0110',
            'RECEPTACION' => '1820',
            'RESISTENCIA' => '0412',
            'RESPONSABILIDAD_TEC' => '0820',
            'RETENCION_EDA' => '1150',
            'RETENCION_TEN' => '1151',
            'REVELACION' => '0610',
            'ROBO_B' => '181A',
            'ROBO_AHO' => '181S',
            'ROBO_HAB' => '1819',
            'ROBO_AUT' => '1816',
            'ROBO_COM' => '181B',
            'ROBO_CH' => '182H',
            'ROBO_CHT' => '182K',
            'ROBO_ATM' => '182J',
            'ROBO_VEN' => '182I',
            'ROBO_ESC' => '181D',
            'ROBO_GAS' => '181F',
            'ROBO_IND' => '181C',
            'ROBO_IB' => '181W',
            'ROBO_REL' => '181X',
            'ROBO_VEH' => '181Y',
            'ROBO_OFI' => '181E',
            'ROBO_PRI' => '182L',
            'ROBO_TALL' => '181G',
            'ROBO_PUB' => '182B',
            'ROBO_VPUB' => '182A',
            'ROBO_T' => '1818',
            'ROBO_TP' => '181R',
            'ROBO_TPC' => '181Q',
            'ROBO_TPT' => '181P',
            'ROBO_TRA' => '1815',
            'ROBO_V' => '1817',
            'ROBO_CAL' => '1812',
            'ROBO_AS' => '181N',
            'ROBO_AUTP' => '181M',
            'ROBO_COB' => '181L',
            'ROBO_DOC' => '181I',
            'ROBO_HID' => '181O',
            'ROBO_MOT' => '181H',
            'ROBO_REM' => '181K',
            'ROBO_TRAC' => '182G',
            'ROBO_USO' => '2350',
            'ROBO_DV' => '1814',
            'ROBO_MAQ' => '181J',
            'ROBO_CAR' => '181Z',
            'ROBO_GTEN' => '1813',
            'ROBO_TRAI' => '182E',
            'ROBO_COL' => '182D',
            'ROBO_PIND' => '182C',
            'ROBO_DES' => '181U',
            'ROBO_CY' => '181V',
            'ROBO_EQU' => '181T',
            'ROBO_SIM' => '1811',
            'SABOTAJE' => '2590',
            'SECUESTRO' => '1320',
            'SECUESTRO_REH' => '1325',
            'SECUESTRO_TEN' => '1321',
            'SECUESTRO_EXP' => '1323',
            'SECUESTRO_EXT' => '1324',
            'SECUESTRO_D' => '1326',
            'SECUESTRO_SIM' => '1322',
            'SECUESTRO_SP' => '1328',
            'SEDICION' => '0120',
            'SIMULACION_DOC' => '0924',
            'SIMULACION_P' => '2520',
            'SUMINISTRO_MED' => '2550',
            'TPE' => '1710',
            'TERRORISMO' => '0450',
            'TERRORISMO_GT' => '0451',
            'TRAFICO_INF' => '2420',
            'TRAFICO_EDA' => '1330',
            'TRATA' => '0532',
            'ULTRAJE' => '0460',
            'ULTRAJE_MP' => '0510',
            'USO_FAC' => '2380',
            'USO_EME' => '0341',
            'USO_EST' => '0950',
            'USURA' => '1860',
            'USURPACION_SCON' => '2634',
            'USURPACION_PRO' => '0940',
            'VAGANCIA' => '1010',
            'VARIACION_DP' => '0930',
            'VIOLACION' => '1410',
            'VIOLACION_PRI' => '2570',
            'VIOLACION_CORR' => '0330',
            'VIOLACION_S' => '0430',
            'VIOLACION_PAR' => '1412',
            'VIOLACION_TEN' => '1411',
            'VIOLACION_EQU' => '1413',
            'VIOLENCIA_SEX' => '0553',
            'VIOLENCIA_FAM' => '1160',
            'VIOLENCIA_MUJ' => '1180',
            'VIOLENCIA_VIC' => '1170'
        ];
        $caseDelitos = "CASE ";
        foreach ($delitosCategorias as $categoria => $id) {
            $caseDelitos .= "WHEN AVE_MUNICIPIOS.IDDELITO IN ('" . $id . "') THEN '$categoria' ";
        }
        $caseDelitos .= "ELSE 'OTRO DELITO' END";
        
        $delitos = AveMunicipio::leftJoin('DELITOS as d', 'AVE_MUNICIPIOS.IDDELITO', '=', 'd.IDDELITO')
        ->leftjoin('SUBPRO AS sb', 'AVE_MUNICIPIOS.IDSUBPRO', '=', 'sb.IDSUBPRO')
        ->leftjoin('MESES AS m', 'AVE_MUNICIPIOS.MES', '=', 'm.IDMES')
        //->join('SUBPRO as sb', 'sb.IDSUBPRO', '=', 'AVE_MUNICIPIOS.IDSUBPRO')
        ->whereBetween('AVE_MUNICIPIOS.ANIO', [$year - 2, $year])
        ->whereBetween('AVE_MUNICIPIOS.MES', [$mesInicial, $mesFinal])
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('DELITOS AS d')
                ->whereColumn('d.IDDELITO', 'AVE_MUNICIPIOS.IDDELITO')
                ->where('d.ESTATUS', 1);
        })
        ->selectRaw("
            $caseDelitos AS Delito,
            AVE_MUNICIPIOS.IDSUBPRO,
            AVE_MUNICIPIOS.ANIO,
            SUM(AVE_MUNICIPIOS.CANTIDAD) AS CANTIDAD    
        ")
        ->groupBy('AVE_MUNICIPIOS.IDSUBPRO', 'AVE_MUNICIPIOS.ANIO', 
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
