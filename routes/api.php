<?php
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\FondoFinancieroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//pruebas
Route::get('/prueba', [PruebaController::class, 'obternerPrueba']);
Route::get('/prueba/{id}', [PruebaController::class, 'findById']);

Route::post('/insertar-prueba', [PruebaController::class, 'insertarPrueba']);
Route::put('/update-prueba/{id}', [PruebaController::class, 'updatePrueba']);


Route::prefix('v1')->group(function () {
    Route::get('/prueba', [PruebaController::class, 'obternerPrueba']);
    Route::post('/insertar-prueba', [PruebaController::class, 'insertarPrueba']);
});
//fin de pruebas



Route::post('/register', [AuthController::class,'register']);
/* Route::post('login', [AuthController::class, 'login']); */
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
});

//fondo financiero
Route::post('/fondofinanciero',[FondoFinancieroController::class,'SaveFondoFinanciero']);