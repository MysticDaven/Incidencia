<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('home.ingresarRangos');
});



Route::controller(HomeController::class)->name('home.')->group(function(){
    Route::get('/rangos','ingresarRangos')
    ->name('ingresarRangos');

    Route::post('/procesar-rangos', 'procesarRangos')
        ->name('procesarRangos');

    Route::get('/reporte','ingresarReporte')
        ->name('ingresarReporte');

    Route::post('/procesar-reporte','procesarReporte')    
        ->name('procesarReporte');
});

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

Route::controller(ReporteController::class)->name('reporte.')->group(function () {
    Route::get('/export-anio/{anio}', 'exportAnio')
    ->name('exportAnio');

    Route::get('/reporte/export/delitos', 'exportPrincipalesDelitos')
        ->name('principalesDelitos');

    Route::get('/reporte/export/delitos_mes/', 'exportPrincipalesDelitosMes')
        ->name('principalesDelitosMes');

    Route::get('/reporte/export/alto_impacto', 'exportAltoImpacto')    
        ->name('altoImpacto');

    Route::get('/reporte/export/secuestros/', 'exportSecuestros')
        ->name('secuestros');

    Route::get('/reporte/export/extorsiones/', 'exportExtorsiones')
        ->name('extorsiones');

    Route::get('/reporte/export/homicidios/', 'exportHomicidios')
        ->name('homicidios');

    Route::get('/reporte/export/privacion/', 'exportPrivacion')
        ->name('privacion');

    Route::get('/reporte/export/lesiones/', 'exportLesiones')
        ->name('lesiones');

    Route::get('/reporte/export/robo_modalidad/', 'exportRoboModalidad')
        ->name('roboModalidad');

    Route::get('/reporte/export/informativo/', 'exportInformativo')
        ->name('informativo');

    Route::get('/reporte/export/incremento/', 'exportIncremento')
        ->name('incremento');

    Route::get('/reporte/export/incidencia/', 'exportIncidencia')
        ->name('incidencia');

    Route::get('/reporte/export/delitos_modalidad/', 'exportDelitosModalidad')
        ->name('delitosModalidad');

    Route::get('/reporte/export/trata_personas/', 'exportTrata')
        ->name('trata');

    Route::get('/reporte/export/feminicidios/', 'exportFeminicidios')
        ->name('feminicidios');

    Route::get('/reporte/export/informativo_acumulado/', 'exportInformativoAcumulado')
        ->name('informativoAcumulado');

    Route::get('/reporte/export/robo_modalidad_mes/', 'exportRoboModalidadMes')
        ->name('roboModalidadMes');

    Route::get('/reporte/export/graficas/', 'exportGraficas')
        ->name('graficas');
});