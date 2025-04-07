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
    }

    public function exportAltoImpacto (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportAltoImpacto($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportPrincipalesDelitosMes (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportPrincipalesDelitosMes($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportSecuestros (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportSecuestros($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportExtorsiones (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportExtorsiones($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function exportHomicidios(ExportService $exportService)
    {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
    
        try {
            $zipPath = $exportService->exportHomicidios($rangos, $reporte);
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function exportPrivacion (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportPrivacion($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportLesiones (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);

        try {
            $zipPath = $exportService->exportLesiones($rangos, $reporte);
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportRoboModalidad (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportRoboModalidad($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportInformativo (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportInformativo($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function exportIncremento (ExportService $exportService) {
    //     $rangos = session('rangos', []);
    //     $reporte = session('reporte', []);        

    //     // try {
    //     //     return $exportService->exportIncremento($rangos, $reporte);
    //     // } catch (\Exception $e) {
    //     //     return response()->json([
    //     //         'error' => $e->getMessage()
    //     //     ], 500);
    //     // }

    //     try {
    //         return $exportService->exportDecremento($rangos, $reporte);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }        
    // }

    public function exportIncremento (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);

        try {
            $zipPath = $exportService->exportIncremento($rangos, $reporte);
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportIncidencia (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportIncidencia($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportDelitosModalidad (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportDelitosModalidad($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportTrata (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportTrata($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportFeminicidios (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportFeminicidios($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportInformativoAcumulado (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportInformativoAcumulado($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportRoboModalidadMes (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportRoboModalidadMes($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportGraficas (ExportService $exportService) {
        $rangos = session('rangos', []);
        $reporte = session('reporte', []);
        try {
            return $exportService->exportGraficas($rangos, $reporte);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
