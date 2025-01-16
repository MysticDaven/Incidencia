<?php

namespace App\Http\Controllers;

use App\Exports\ReporteExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    //
    public function exportExcel(Request $request) {
        return Excel::download(new ReporteExport($request->all()), 'reporte.xlsx');
    }
}
