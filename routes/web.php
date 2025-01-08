<?php

use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('reporte.ingresarRangos');
});

Route::get('/rangos', [ReporteController::class, 'ingresarRangos'])
    ->name('reporte.ingresarRangos');

Route::post('/procesar-rangos', [ReporteController::class, 'procesarRangos'])
    ->name('reporte.procesarRangos');

Route::get('/reporte', [ReporteController::class, 'ingresarReporte'])
    ->name('reporte.ingresarReporte');

Route::post('/procesar-reporte', [ReporteController::class, 'procesarReporte'])    
    ->name('reporte.procesarReporte');