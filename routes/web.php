<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('home.ingresarRangos');
});

Route::get('/rangos', [HomeController::class, 'ingresarRangos'])
    ->name('home.ingresarRangos');

Route::post('/procesar-rangos', [HomeController::class, 'procesarRangos'])
    ->name('home.procesarRangos');

Route::get('/reporte', [HomeController::class, 'ingresarReporte'])
    ->name('home.ingresarReporte');

Route::post('/procesar-reporte', [HomeController::class, 'procesarReporte'])    
    ->name('home.procesarReporte');

// Route::post('/reporte/{reporte}', ReporteController::class);

Route::post('/export-excel', [ReporteController::class, 'exportExcel']);

//---------------------------EJEMPLO DE EXPORTAR EN EXCEL-----------------------------
// Route::get('/export-anio/{anio}', function ($anio) {
//     try {
//         // Inyectar TemporaryFileFactory desde el contenedor
//         $temporaryFileFactory = app(TemporaryFileFactory::class);

//         // Crear una instancia de AniosExport pasando ambos argumentos
//         $export = new AniosExport($anio, $temporaryFileFactory);

//         // Descargar el archivo Excel
//         return Excel::download($export, "anios_{$anio}.xlsx");
//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// });

Route::get('/export-anio/{anio}', [ReporteController::class, 'exportAnio'])
    ->name('reporte.exportAnio');

Route::get('/reporte/export/', [ReporteController::class, 'exportPrincipalesDelitos'])
    ->name('reporte.principalesDelitos');