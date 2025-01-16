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
