<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PRUEBA;

class pruebacontroller extends Controller
{
    public function obternerPrueba()
    {
        try {
            return PRUEBA::all();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        // return json_encode(array('mensaje'=>'holaaaa'));
    }

    public function findById($id)
    {
        try {
            return PRUEBA::find($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function insertarPrueba(Request $request)
    {
        $request->validate([
            "nombre" => "required",
        ]);

        try {
            $tablaModel = new PRUEBA;
            $tablaModel->nombre = $request->input('nombre');
            $tablaModel->save();

            $informacionNuevoREgistro = PRUEBA::find($tablaModel->id);

            return response()->json([
                'info' => $informacionNuevoREgistro,
                'url' => 'http://127.0.0.1:8000/api/prueba/' . $informacionNuevoREgistro->id,
                'mensaje' => 'Registro insertado con éxito'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updatePrueba(Request $request, $id)
    {
        $request->validate([
            "nombre" => "required",
        ]);

        try {
            $informacionNuevoREgistro = PRUEBA::find($id);

            if (!$informacionNuevoREgistro) {
                return json_encode([
                    "id" => $id,
                    "mensaje" => "No existe el registro para actualizar"
                ], 404);
            }

            $informacionNuevoREgistro->nombre = $request->input("nombre");
            $informacionNuevoREgistro->save();
            return response()->json([
                'info' => $informacionNuevoREgistro,
                'url' => 'http://127.0.0.1:8000/api/prueba/' . $informacionNuevoREgistro->id,
                'mensaje' => 'Registro actualizado con exito'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
