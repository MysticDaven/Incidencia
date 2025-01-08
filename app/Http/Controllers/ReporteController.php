<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReporteController extends Controller
{
    // Métodos para ingresar los rangos    
    public function ingresarRangos () {
        $actualYear = date('Y');
        $months = [
            0 => 'Enero',
            1 => 'Febrero',
            2 => 'Marzo',
            3 => 'Abril',
            4 => 'Mayo',
            5 => 'Junio',
            6 => 'Julio',
            7 => 'Agosto',
            8 => 'Septiembre',
            9 => 'Octubre',
            10 => 'Noviembre',
            11 => 'Diciembre'
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
        
        return redirect()->route('reporte.ingresarReporte');
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

        return response()->json([
            'mensaje' => 'Datos recibidos con éxito.',
            'rangos' => $rangos,
            'reporte' => $reporte,
        ]);        
    }
}