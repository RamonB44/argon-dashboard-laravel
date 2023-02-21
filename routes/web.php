<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\Auxliliar\DiasFeriadosController;
use App\Http\Controllers\Auxliliar\TipoRegistroController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\EmployesController;
use App\Http\Controllers\FuncionController;
use App\Http\Controllers\GerenciaController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ManageProcessController;
use App\Http\Controllers\ProcesosController;
use App\Http\Controllers\SedesController;
use App\Http\Controllers\UsersController;


Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');

Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest')->name('register.perform');
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login.perform');
Route::get('/reset-password', [ResetPassword::class, 'show'])->middleware('guest')->name('reset-password');
Route::post('/reset-password', [ResetPassword::class, 'send'])->middleware('guest')->name('reset.perform');
Route::get('/change-password', [ChangePassword::class, 'show'])->middleware('guest')->name('change-password');
Route::post('/change-password', [ChangePassword::class, 'update'])->middleware('guest')->name('change.perform');
Route::get('/dashboard', [HomeController::class, 'index'])->name('home')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {

    Route::get('/virtual-reality', [PageController::class, 'vr'])->name('virtual-reality');
    Route::get('/rtl', [PageController::class, 'rtl'])->name('rtl');
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile-static', [PageController::class, 'profile'])->name('profile-static');
    Route::get('/sign-in-static', [PageController::class, 'signin'])->name('sign-in-static');
    Route::get('/sign-up-static', [PageController::class, 'signup'])->name('sign-up-static');
    Route::get('/{page}', [PageController::class, 'index'])->name('page');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::controller(UsersController::class)->prefix('users')->group(function () {
        Route::get('/index',  'show')->name('users.index');
        Route::get('/getTable', 'getTable')->name('users.getTable');
        Route::get('/create_form', 'create_form')->name('users.create_form');
        Route::post('/create', 'create')->name('users.create');
        Route::get('/edit/{id}', 'update')->name('users.update');
        Route::post('/edit/{id}', 'update')->name('users.update');
        Route::get('/delete/{id}', 'delete')->name('users.delete');

        Route::get('/profile', 'profile')->name('users.profile');
        Route::post('/profile',  'profile')->name('users.profile');
        Route::get('/config',   'config')->name('users.config');
        Route::post('/config', 'config')->name('users.config');
    });


    Route::controller(GroupController::class)->prefix('groups')->group(function () {
        Route::get('/index', 'index')->name('groups.index');
        Route::get('/getTable', 'getTable')->name('group.getTable');
        Route::get('/create', 'create')->name('group.create');
        Route::post('/create', 'create')->name('group.create');
        Route::get('/edit/{id}', 'update')->name('group.update');
        Route::post('/edit/{id}', 'update')->name('group.update');
        Route::get('/delete/{id}', 'delete')->name('group.delete');
    });

    Route::controller(GerenciaController::class)->prefix('gerencia')->group(function () {
        Route::get('/index', 'index')->name('gerencia.index');
        Route::get('/getTable', 'getTable')->name('gerencia.getTable');
        Route::get('/create', 'create')->name('gerencia.create');
        Route::post('/create', 'create')->name('gerencia.create');
        Route::get('/update/{id}', 'update')->name('gerencia.update');
        Route::post('/update/{id}', 'update')->name('gerencia.update');
        Route::get('/delete/{id}', 'delete')->name('gerencia.delete');

        Route::get('/getData', 'getData')->name('gerencia.getData');
    });

    Route::controller(SedesController::class)->prefix('sedes')->group(function () {
        Route::get('/index', 'index')->name('sedes.index');
        Route::get('/getTable', 'getTable')->name('sedes.getTable');
        Route::get('/create', 'create')->name('sedes.create');
        Route::post('/create', 'create')->name('sedes.create');
        Route::get('/update/{id}', 'update')->name('sedes.update');
        Route::post('/update/{id}', 'update')->name('sedes.update');
        Route::get('/delete/{id}', 'delete')->name('sedes.delete');
    });

    Route::controller(ProcesosController::class)->prefix('procesos')->group(function () {
        Route::get('/index', 'index')->name('procesos.index');
        Route::get('/getTable', 'getTable')->name('procesos.getTable');
        Route::get('/create', 'create')->name('procesos.create');
        Route::post('/create', 'create')->name('procesos.create');
        Route::get('/update/{id}', 'update')->name('procesos.update');
        Route::post('/update/{id}', 'update')->name('procesos.update');
        Route::get('/delete/{id}', 'delete')->name('procesos.delete');
        Route::get('/getData', 'getData')->name('procesos.getData');
    });

    Route::controller(AreaController::class)->prefix('areas')->group(function () {
        Route::get('/index', 'index')->name('area.index');
        Route::get('/getTable', 'getTable')->name('area.getTable');
        Route::get('/getTable/{id_sede}', 'getTable')->name('area.getTable2');
        Route::get('/getTable/{id_sede}/{id_proceso}', 'getTable')->name('area.getTable3');
        Route::get('/create', 'create')->name('area.create');
        Route::post('/create', 'create')->name('area.create');
        // Route::get('/update/{id}', 'AreaController@update')->name('area.update');
        Route::get('/update/{id}/{id_sede}', 'update')->name('area.update');
        Route::get('/update/{id}/{id_sede}/{id_proceso}', 'update')->name('area.update');
        Route::post('/update/{id}', 'update')->name('area.update');
        Route::post('/update/{id}/{id_sede}', 'update')->name('area.update');
        Route::post('/update/{id}/{id_sede}/{id_proceso}', 'update')->name('area.update');
        Route::get('/delete/{id}', 'delete')->name('area.delete');
        Route::get('/delete/{id}/{id_sede}', 'delete')->name('area.delete');
        Route::post('/group', 'group')->name('area.group');

        Route::get('/getData/{id_gerencia}', 'getData')->name('areas.getData');
        Route::get('/loadAreas/{id_sede}/{id_proceso}', 'loadAreas')->name('areas.loadAreas');

        // this routes is for gestion areas
        Route::get('/gestion', 'gestion')->name('areas.gestion');
        Route::get('/gestion/getTable', 'gestiongetTable')->name('areas.gestion.getTable');
        Route::get('/gestion/create', 'gestionCreate')->name('areas.gestion.create');
        Route::post('/gestion/create', 'gestionCreate')->name('areas.gestion.create');
        Route::get('/gestion/update/{id}', 'gestionUpdate')->name('areas.gestion.update');
        Route::post('/gestion/update/{id}', 'gestionUpdate')->name('areas.gestion.update');
        Route::get('/gestion/delete/{id}', 'gestionDelete')->name('areas.gestion.delete');

        Route::post('/getFunctions', 'AreaController@getFunctions')->name('areas.getFunctions');
    });

    Route::controller(FuncionController::class)->prefix('funcion')->group(function () {
        Route::get('/index', 'index')->name('funcion.index');
        Route::get('/getTable', 'getTable')->name('funcion.getTable');
        Route::get('/create', 'create')->name('funcion.create');
        Route::post('/create', 'create')->name('funcion.create');
        Route::get('/update/{id}', 'update')->name('funcion.update');
        Route::post('/update/{id}', 'update')->name('funcion.update');
        Route::get('/delete/{id}', 'delete')->name('funcion.delete');
        Route::get('/loadByArea/{id_area}', 'loadByArea')->name('funcion.loadByArea');
        Route::get('/loadByArea/{id_area}/{id_sede}/{id_proceso}', 'loadByArea')->name('funcion.loadByArea');
        Route::get('/getData/{id_area}', 'loadByArea')->name('funcion.getData');
        // Route::get('/getData/{id_funcion}','FuncionController@loadByArea')->name('')
        Route::prefix('gestion')->group(function () {
            Route::get('/index', function () {
                return view('funcion.gestion.index');
            })->name('funcion.destajo.index');
            Route::get('/getTable', 'ggetTable')->name('funcion.gestion.getTable');
            Route::get('/create', 'gcreate')->name('funcion.gestion.create');
            Route::post('/create', 'gcreate')->name('funcion.gestion.create');
            Route::get('/update/{id}', 'gupdate')->name('funcion.gestion.update');
            Route::post('/update/{id}', 'gupdate')->name('funcion.gestion.update');
            Route::get('/delete/{id}', 'gdelete')->name('funcion.gestion.delete');
        });
    });

    Route::prefix('auxiliar')->group(function () {
        Route::controller(TipoRegistroController::class)->prefix('treg')->group(function () {
            Route::get('/index', 'index')->name('treg.index');
            Route::get('/getTable', 'getTable')->name('treg.getTable');
            Route::get('/create', 'create')->name('treg.create');
            Route::post('/create', 'create')->name('treg.create');
            Route::get('/update/{id}', '@update')->name('treg.update');
            Route::post('/update/{id}', 'update')->name('treg.update');
            Route::get('/delete/{id}', 'delete')->name('treg.delete');
        });
        Route::controller(DiasFeriadosController::class)->prefix('offday')->group(function () {
            Route::get('/index', 'index')->name('offday.index');
            Route::get('/getTable', 'getTable')->name('offday.getTable');
            Route::get('/create', 'create')->name('offday.create');
            Route::post('/create', 'create')->name('offday.create');
            Route::get('/update/{id}', 'update')->name('offday.update');
            Route::post('/update/{id}', 'update')->name('offday.update');
            Route::get('/delete/{id}', 'delete')->name('offday.delete');
        });
    });

    Route::controller(EmployesController::class)->prefix('employes')->group(function () {
        Route::get('/index', 'index')->name('employes.index');
        Route::get('/getTable', 'getTable')->name('employes.getTable');
        Route::get('/create', 'create')->name('employes.create');
        Route::post('/create', 'create')->name('employes.create');
        Route::get('/update/{id}', 'update')->name('employes.update');
        Route::post('/update/{id}', 'update')->name('employes.update');
        Route::get('/delete/{id}', 'delete')->name('employes.delete');
        Route::post('/massivedel', 'delete_chunck')->name('employes.massiveDelete');
        Route::post('/massiverest', 'restore_chunck')->name('employes.massiveRestore');
        Route::get('/code/{code}', 'getEmploye')->name('employes.getEmploye');
        Route::get('/generateCode/{id}', 'generateNewCode')->name('employes.generateCode');
        Route::post('/import', 'importEmploye')->name('employes.import');
        Route::get('/getTableArea/{id_area}', 'massive')->name('employes.employesbyArea');
        Route::post('/group', 'group')->name('employes.group');

        Route::post('/process', 'process')->name('employes.process');
        //procesos y empleados pendiente
        Route::get('/procesos/{id_employe}', 'procedures')->name('employes.procedures');

        Route::controller(ManageProcessController::class)->prefix('manageprocess')->group(function () {
            Route::get('/index', 'index')->name('manageprocess.index');
            Route::get('/employesCustom', 'getCustomEmployes')->name('manageprocess.getEmployes');
            Route::get('/getregister/{code}', 'getregister')->name('manageprocess.getregister');
            // Route::get('/massive','ManageProcessController@massive')->name('manageprocess.massive');
            Route::post('/massiveregister', 'massiveReg')->name('manageprocess.massiveReg');
            Route::post('/massivechange','changeMasive')->name('manageprocess.changeMasive');
            Route::post('/import','importProcess')->name('manageprocess.import');
        });

        Route::prefix('reportes')->group(function () {
            Route::get('/index', 'reportes')->name('employes.reportes');
        });
    });


});
