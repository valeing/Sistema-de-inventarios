<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bien;
class ResguardanteDashboardController extends Controller
{
    //

public function index()
{
    $resguardante = auth()->user()->resguardante;

    $totalBienes = $resguardante
        ? Bien::where('resguardante_id', $resguardante->id)->count()
        : 0;

    return view('resguardante.dashboard', compact('totalBienes'));
}

}
