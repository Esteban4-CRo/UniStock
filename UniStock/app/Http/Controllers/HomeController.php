<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Entrada;
use App\Models\Salida;

class HomeController extends Controller
{
    public function index()
    {
        $totalProductos = Producto::count();
        $totalEntradas = Entrada::count();
        $totalSalidas = Salida::count();
        
        $productos = Producto::latest()->take(5)->get();
        $entradas = Entrada::with('producto')->latest()->take(5)->get();
        $salidas = Salida::with('producto')->latest()->take(5)->get();

        return view('home', compact(
            'totalProductos', 
            'totalEntradas', 
            'totalSalidas',
            'productos',
            'entradas',
            'salidas'
        ));
    }
}