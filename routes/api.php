<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/economic', function (Request $request) {

    $id = $request->id;
    $time = now()->getTimestampMs();
    $response = Http::get("http://apps.tarjetaferia.com/monterrey/pruebas/enlinea_posicion_v1/tiemporeal.php?rs=ConsultaEconomicos&rst=&rsrnd=$time&rsargs[]=25.677269657380343%2C-100.28281688690186&rsargs[]=$id");
    $slice = Str::between($response->body(), '+:var res = \'', '\'; res;');
    $economics = explode("@", $slice);
    $result = [];
    foreach ($economics as $economic) {
        $data = explode("|", $economic);
        
        if ($data[0] !== "") {
            array_push($result,[
                "numero" => $data[0],
                "velocidad" => $data[1],
                "hora" => explode(" ", $data[2])[1],
                "sentido" => $data[3] == 1 ? "ida" : "regreso",
                "latitud" => $data[4],
                "longitud" => $data[5],
                "distacia" => $data[6],
                "tiempo de llegada" => $data[7],
                "tramo" => $data[8],
            ]);
        }
    }
    return response()->json($result);
});

Route::get('/route', function (Request $request) {

    $id = $request->id;
    $time = now()->getTimestampMs();
    $response = Http::get("http://apps.tarjetaferia.com/monterrey/pruebas/enlinea_posicion_v1/tiemporeal.php?rs=DibujaRuta&rst=&rsrnd=$time&rsargs[]=$id");
    $slice = Str::between($response->body(), '+:var res = \'', '\'; res;');
    $points = explode("@", $slice);
    $result = [];
    foreach ($points as $point) {
        $rawData = explode("|", $point);
        if ($rawData[0] !== ""){
            $cordinates = explode(",", $rawData[0]);
            array_push($result,[
                "sentido" => $rawData[1] == 1 ? "ida" : "regreso",
                "latitud" => $cordinates[0],
                "longitud" => $cordinates[1],
            ]);
        }
    }
    return response()->json($result);
});