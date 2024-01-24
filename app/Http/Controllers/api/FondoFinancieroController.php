<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FondoFinancieroModel;
use Symfony\Component\HttpFoundation\Response;

class FondoFinancieroController extends Controller
{
    public function SaveFondoFinanciero(Request $request)
    {
       // return 'holaxx';
        $request->validate([
            'nombre_cuenta' => 'required',
            'cuenta_bancaria' => 'required',
            'identificacion' => 'required',
            'cantidad' => 'required',
            'num_orden' => 'required',
            'comprobante' => 'required',
        ]);
        try {
            $fondo = new FondoFinancieroModel();
            $fondo->nombre_cuenta = $request->nombre_cuenta;
            $fondo->cuenta_bancaria = $request->cuenta_bancaria;
            $fondo->identificacion = $request->identificacion;
            $fondo->cantidad = $request->cantidad;
            $fondo->num_orden = $request->num_orden;
            $fondo->comprobante = $request->comprobante;

            $fondosSaved = $fondo->save();
            return response(["info" => $fondosSaved], $fondosSaved ? Response::HTTP_CREATED : Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
