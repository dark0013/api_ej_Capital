<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\RegistroMailable;
use App\Models\Usuarios;
use App\Models\Autorizacion;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // return $$request;
        $request->validate([
            'nombres' => 'required',
            'cedula' => 'required|unique:tbl_adm_usuarios,cedula',
            'celular' => 'required',
            'email' => 'required|unique:tbl_adm_usuarios,email',
            'residencia' => 'required',
            'pass' => 'required',
            'conf_pass' => 'required',
            'filecedula' => 'required',
            'fileselfie' => 'required'
        ]);

        try {
            $user = new Usuarios();
            $user->nombres = $request->nombres;
            $user->cedula = $request->cedula;
            $user->celular = $request->celular;
            $user->email = $request->email;
            $user->residencia = $request->residencia;

            $user->pass = Hash::make($request->pass);
            $user->conf_pass = Hash::make($request->conf_pass);


            $user->fileselfie = $request->fileselfie;

            //NOTA: corregir y revisar por apuro repeti el mismo codigo para los 2 documentos
            $imagenBase64 = $request->filecedula;

            if (strpos($imagenBase64, 'data:image/jpg;base64,') === 0 || strpos($imagenBase64, 'data:image/jpeg;base64,') === 0 || strpos($imagenBase64, 'data:image/png;base64,') === 0) {

                //  $datosImagen = substr($imagenBase64, strlen('data:image/png;base64,'));
                $datosImagen = substr($imagenBase64, strpos($imagenBase64, ',') + 1);
                $extencion = pathinfo(parse_url($imagenBase64, PHP_URL_PATH), PATHINFO_EXTENSION);

                $archivoBinario = base64_decode($datosImagen);
            } else {
                $archivoBinario = base64_decode($request->filecedula);
            }

            $nombreArchivo = uniqid() . '.jpg';

            $rutaGuardar = public_path('app/archivos/');


            if (!file_exists($rutaGuardar)) {
                mkdir($rutaGuardar, 0755, true);
            }

            file_put_contents($rutaGuardar . $nombreArchivo, $archivoBinario);

            $rutaArchivoRelativa = 'public/app/archivos/' . $nombreArchivo;

            $user->filecedula = $rutaArchivoRelativa;


            //selfi optimizar
            $imagenBase64 = $request->fileselfie;

            if (strpos($imagenBase64, 'data:image/jpg;base64,') === 0 || strpos($imagenBase64, 'data:image/jpeg;base64,') === 0 || strpos($imagenBase64, 'data:image/png;base64,') === 0) {

                $datosImagen = substr($imagenBase64, strpos($imagenBase64, ',') + 1);
                $extencion = pathinfo(parse_url($imagenBase64, PHP_URL_PATH), PATHINFO_EXTENSION);

                $archivoBinario = base64_decode($datosImagen);
            } else {
                $archivoBinario = base64_decode($request->fileselfie);
            }

            $nombreArchivo = uniqid() . '.jpg';

            $rutaGuardar = public_path('app/archivos/');


            if (!file_exists($rutaGuardar)) {
                mkdir($rutaGuardar, 0755, true);
            }

            file_put_contents($rutaGuardar . $nombreArchivo, $archivoBinario);

            $rutaArchivoRelativa = 'public/app/archivos/' . $nombreArchivo;

            $user->fileselfie = $rutaArchivoRelativa;

            $userSaved = $user->save();


            /*       if ($userSaved) {
                      // Send email only if the user is saved successfully
                      Mail::to($request->email)->send(new RegistroMailable);
                  }
           */
            /*  return response(["info" => $user, "mensaje" => $userSaved ? "Usuario registrado y mensaje enviado" : "Error al registrar usuario"], $userSaved ? Response::HTTP_CREATED : Response::HTTP_INTERNAL_SERVER_ERROR); */
            return response(["info" => $user], $userSaved ? Response::HTTP_CREATED : Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function guardarArchivo(Request $request)
    {
        try {
            $archivoBinario = base64_decode($request->filecedula);

            // Generar un nombre único para el archivo
            $nombreArchivo = uniqid() . '.jpg'; // Puedes ajustar la extensión según el tipo de archivo

            // Ruta de la carpeta donde deseas guardar los archivos
            $rutaGuardar = public_path('app/archivos/');

            // Verificar si la carpeta existe, y si no, crearla
            if (!file_exists($rutaGuardar)) {
                mkdir($rutaGuardar, 0755, true);
            }

            // Guardar el archivo en la carpeta deseada
            file_put_contents($rutaGuardar . $nombreArchivo, $archivoBinario);

            // Ruta relativa del archivo para almacenar en la base de datos
            $rutaArchivoRelativa = 'app/archivos/' . $nombreArchivo;

            return response()->json(['message' => 'Archivo guardado correctamente', 'rutaArchivo' => $rutaArchivoRelativa]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar el archivo: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'cedula' => 'required',
            'contrasenia' => 'required',
        ]);


        try {

            $user = Usuarios::where('cedula', $credentials['cedula'])->first();

            if (!$user || !Hash::check($credentials['contrasenia'], $user->pass)) {
                return response(["msjResponse" => "Credenciales Incorrectas", "codResponse"=>"02"], Response::HTTP_UNAUTHORIZED);
            }

            // Autenticación exitosa
            $token = $user->createToken('token')->plainTextToken;
            // Remover el prefijo "43|"
            $token = explode('|', $token)[1];

            $seguro = new Autorizacion();
            $seguro->cedula = $credentials['cedula'];
            $seguro->token = $token;
            $seguro->save();

            $cookie = cookie('cookie_token', $token, 60 * 24);



            return response(["token" => $token,"msjResponse" => "Transaccion ok", "codResponse"=>"00"], Response::HTTP_OK)->withoutCookie($cookie);
        } catch (\Exception $e) {
            return response()->json(['msjResponse' => $e->getMessage(), "codResponse"=>"99"], 500);
        }
    }
    public function userProfile(Request $request)
    {
        return response()->json([
            "message" => "userProfile OK",
            "userData" => auth()->user()
        ], Response::HTTP_OK);
    }

    public function logout()
    {
        $cookie = Cookie::forget('cookie_token');
        return response(["message" => "Cierre de sesión OK"], Response::HTTP_OK)->withCookie($cookie);
    }
}
