<?php

namespace App\Http\Controllers;

use App\Exports\ReporteExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    // Métodos para ingresar los rangos    
    public function ingresarRangos () {
        $actualYear = date('Y');
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        $years = [];
        for ($i = 2016; $i <= $actualYear; $i++) {
            array_push($years, $i);
        }
        return view('rangos', compact(['months', 'years']));
    }

    public function procesarRangos (Request $request) {
        $validated = $request->validate([
            'reporte_anio' => 'required|numeric',
            'mes_inicial' => 'required|integer|min:0|max:12',
            'mes_final' => 'required|integer|min:0|max:12|gte:mes_inicial',
        ]);        

        $request->session()->put('rangos', $validated);
        // $request->session()->put('datosPrevios', $request->only(['reporte_anio', 'mes_inicial', 'mes_final'])); only obtiene solo datos del $request, pero es mejor y más limpio validarlos antes
        
        return redirect()->route('home.ingresarReporte');
    }

    public function ingresarReporte () {
        $reportes = [
            1 => 'Principales Delitos Comparativo',
            2 => 'Principales Delitos por Mes',
            3 => 'Delitos de Alto Impacto',
            4 => 'Secuestros',
            5 => 'Extorsiones',
            6 => 'Homicidios',
            7 => 'Privación de la Libertad',
            8 => 'Lesiones',
            9 => 'Robo por Modalidad',
            10 => 'Robo por Modalidad por Mes',
            11 => 'Informativo Mes',
            12 => 'Informativo Acumulado',
            13 => 'Incremento y Decremento de la Incidencia',
            14 => 'Incidencia Delictiva por Mes',
            15 => 'Total de Delitos por Modalidad',
            16 => 'Trata de Personas',
            17 => 'Feminicidios',
            18 => 'Gráficas Alto Impacto'
        ];
        $rangos = session('rangos', []);

        return view('reportes', compact(['reportes', 'rangos']));
    }

    public function procesarReporte (Request $request) {
        $reporte = $request->validate([
            'reporte' => 'required'
        ]);
        $rangos = session('rangos', []);
        $rangos['reporte_anio'] = $rangos['reporte_anio'];

        $redirecciones = [
            1 => 'reporte.principalesDelitos',
            2 => 'reporte.principalesDelitosMes',
            3 => 'reporte.altoImpacto',
            4 => 'reporte.secuestros',
            5 => 'reporte.extorsiones',
            6 => 'reporte.homicidios',
            7 => 'reporte.privacion',
            8 => 'reporte.lesiones',
            9 => 'reporte.roboModalidad',
            10 => 'reporte.roboModalidadMes',
            11 => 'reporte.informativo',
            12 => 'redireccion 12',
            13 => 'redireccion 13',
            14 => 'redireccion 14',
            15 => 'redireccion 15',
            16 => 'reporte.trata',
            17 => 'reporte.feminicidios',
            18 => 'reporte.graficas'
        ];

        if (array_key_exists($reporte['reporte'], $redirecciones)) {
            session([
                'rangos' => $rangos,
                'reporte' => $reporte
            ]);
            return redirect()->route($redirecciones[$reporte['reporte']]);
        } else {
            return 'No está';
        }
        
        // return response()->json([
        //     'mensaje' => 'Datos recibidos con éxito.',
        //     'rangos' => $rangos,
        //     'reporte' => $reporte,
        // ]);    
    }

    public function exportExcel (Request $request) {
        return Excel::download(new ReporteExport($request->all(), 'reporte.xlsx'));
    }
}