<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DOMPdfController extends Controller
{
    public function index(){
        return view('dompdf.invoice');
    }
}
