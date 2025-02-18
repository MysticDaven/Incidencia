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

Route::get('/reporte/export/delitos', [ReporteController::class, 'exportPrincipalesDelitos'])
    ->name('reporte.principalesDelitos');

Route::get('/reporte/export/delitos_mes/', [ReporteController::class, 'exportPrincipalesDelitosMes'])
    ->name('reporte.principalesDelitosMes');

Route::get('/reporte/export/alto_impacto', [ReporteController::class, 'exportAltoImpacto'])    
    ->name('reporte.altoImpacto');

Route::get('/reporte/export/secuestros/', [ReporteController::class, 'exportSecuestros'])
    ->name('reporte.secuestros');

Route::get('/reporte/export/extorsiones/', [ReporteController::class, 'exportExtorsiones'])
    ->name('reporte.extorsiones');

Route::get('/reporte/export/homicidios/', [ReporteController::class, 'exportHomicidios'])
    ->name('reporte.homicidios');

Route::get('/reporte/export/privacion/', [ReporteController::class, 'exportPrivacion'])
    ->name('reporte.privacion');

Route::get('/reporte/export/lesiones/', [ReporteController::class, 'exportLesiones'])
    ->name('reporte.lesiones');

Route::get('/reporte/export/robo_modalidad/', [ReporteController::class, 'exportRoboModalidad'])
    ->name('reporte.roboModalidad');

Route::get('/reporte/export/informativo/', [ReporteController::class, 'exportInformativo'])
    ->name('reporte.informativo');

Route::get('/reporte/export/incremento/', [ReporteController::class, 'exportIncremento'])
    ->name('reporte.incremento');

Route::get('/reporte/export/incidencia/', [ReporteController::class, 'exportIncidencia'])
    ->name('reporte.incidencia');

Route::get('/reporte/export/delitos_modalidad/', [ReporteController::class, 'exportDelitosModalidad'])
    ->name('reporte.delitosModalidad');

Route::get('/reporte/export/trata_personas/', [ReporteController::class, 'exportTrata'])
    ->name('reporte.trata');

Route::get('/reporte/export/feminicidios/', [ReporteController::class, 'exportFeminicidios'])
    ->name('reporte.feminicidios');

Route::get('/reporte/export/informativo_acumulado/', [ReporteController::class, 'exportInformativoAcumulado'])
    ->name('reporte.informativoAcumulado');

Route::get('/reporte/export/robo_modalidad_mes/', [ReporteController::class, 'exportRoboModalidadMes'])
    ->name('reporte.roboModalidadMes');

Route::get('/reporte/export/graficas/', [ReporteController::class, 'exportGraficas'])
    ->name('reporte.graficas');