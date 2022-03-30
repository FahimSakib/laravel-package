<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class DOMPdfController extends Controller
{
    public function index(){
        return view('dompdf.invoice');
    }

    public function pdf($type = 'stream'){
    
        $pdf = PDF::loadView('dompdf.invoice-pdf');
        return $type == 'stream' ? $pdf->stream() : $pdf->download('invoice.pdf');
    }
}
