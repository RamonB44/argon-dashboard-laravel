<?php

use App\Http\Controllers\API\AsistenciaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::post('login', 'API\Auth\LoginController@login');

// Route::post('refresh', 'API\Auth\LoginController@refresh');

// Route::post('register', 'API\Auth\RegisterController@register');

Route::controller(AsistenciaController::class)->group(function () {
    Route::get('/ingresos/{sede}/{codigo}', 'register')->name('assistance.register-api');

    Route::get('/ingresos/{sede}/{codigo}/{token}', 'register')->name('assistance.register-apiv2');

    // Route::get('/ingresos/{nombre_sede}/{codigo}/{tokem}', 'API\AsistenciaController@create')->name('assistance.register-apiv3');

    Route::post('/searchEmploye', 'searchEmploye')->name('assistance.searchEmploye');

    Route::post('/filterEmployes', 'filterEmployes')->name('assistance.filterEmployes');

    Route::get('/setAssistance/{_token}/{sap_code}/{in_out}/{sede}', 'setAssistance')->name('assistance.setAssistance');

    Route::get('/setAssistance/{_token}/{sap_code}/{in_out}/{sede}/{uts}', 'setAssistance')->name('assistance.setAssistance');

});

Route::get('/employes/{code}',[AsistenciaController::class,'filterEmploye'])->name('employe.filter');

// Route::group(['middleware' => 'auth:api'], function(){

//     Route::post('logout', 'API\Auth\LoginController@logout');

//     Route::get('/getdata','API\UserController@getData')->name('api.getdata');

//     Route::post('/setQuery','API\UserController@setQuery')->name('api.setQuery');

//     Route::prefix('assistance')->group(function () {
//         Route::post('/register', 'API\AsistenciaController@create')->name('assistance.register');

//         Route::get('/checkAsistencia', 'API\AsistenciaController@getData')->name('assistance.checkAsistencia');

//         Route::post('/check', 'API\AsistenciaController@checked')->name('assistance.checked');

//         //Route::get('/setAssistance/{sap_code}/{in_out}', 'API\AsistenciaController@setAssistance')->name('assistance.setAssistance');
//     });

//     Route::prefix('employes')->group(function() {
//         Route::get('/getEmployes', 'API\AsistenciaController@getEmployes')->name('assistance.getEmployes');
//     });
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
