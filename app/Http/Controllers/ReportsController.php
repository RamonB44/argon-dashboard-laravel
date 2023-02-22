<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    //

    public function showProduccion(){
        return view('reports.producction');
    }

    public function showRecursos(){
        return view('reports.recursos');
    }
}
