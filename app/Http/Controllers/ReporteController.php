<?php

namespace App\Http\Controllers;

use App\Exports\ReporteExport;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    //
    public function exportExcel(Request $request) {
        return Excel::download(new ReporteExport($request->all()), 'reporte.xlsx');
    }

    public function exportAnio ($anio, ExportService $exportService) {
        try {
            return $exportService->exportAnio($anio);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function exportPrincipalesDelitos (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);

        try {
            return $exportService->exportPrincipalesDelitos($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
        // return response()->json([
        //     'rangos' => $rangos,
        //     'reporte' => $reporte
        // ]);
    }
}
