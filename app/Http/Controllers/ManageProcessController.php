<?php

namespace App\Http\Controllers;

use App\Models\Employes;
use App\Excel\EmployesToggle;
use App\Excel\ProceduresImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ManageProcessController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(!Auth::user()->hasGroupPermission("viewProcessEmploye")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        return view('employes.process.index');
    }

    public function getCustomEmployes()
    {
        $data = array();
        $headers = array();

        $datos = Employes::all();

        foreach ($datos as $key => $value) {
            # code...
            // $buttons = "";
            $data['data'][$key] = [
                $value->code,
                $value->fullname
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function getTable($id)
    {
        $employe_process = Employes::where('id', '=', $id)->first()->employes_process;

        foreach ($employe_process as $key => $value) {
            # code...
            $data['data'][$key] = [
                $key + 1,
                $value->proceso->description,
                $value->created_at, //until_at
                // $value->deleted_at
            ];
        }
        return response()->json($data, 200, []);
    }

    public function getregister($code)
    {
        $data = array();
        $headers = array();
        $codigo = $code;
        $datos = Employes::where('code', '=', $code)->first()->employes_process()->withTrashed()->get();

        foreach ($datos as $key => $value) {
            # code...

            $data['data'][$key] = [
                $key + 1,
                $value->name,
                Carbon::parse($value->pivot->created_at)->format('d/m/Y'),
                ($value->pivot->deleted_at)?Carbon::parse($value->pivot->deleted_at)->format('d/m/Y H:i:s'):"EN PROCESO",
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function massiveReg(Request $req)
    {
        $datos = array();
        //replace

        // $fecha1 = Carbon::parse($req->d_since_at);
        try {
            //code...
            $fecha2 = Carbon::parse($req->d_until_at);

            if ($req->tipo == 1) {
                //registro
                foreach ($req->ids as $key => $value) {
                    # code...
                    $employe = Employes::where('code', '=', $value)->first();
                    if(!isset($employe->employes_process()->whereNull('procesos_employe.deleted_at')->orderBy('created_at','desc')->first()->pivot)){
                        $employe->employes_process()->attach($req->description, ['created_at' => $fecha2]);
                    }else{
                        DB::table('procesos_employe')->where('id','=',$employe->employes_process()->whereNull('procesos_employe.deleted_at')->orderBy('created_at','desc')->first()->pivot->id)->update(array('deleted_at' => DB::raw('NOW()')));
                        $employe->employes_process()->attach($req->description, ['created_at' => $fecha2]);
                    }
                }
                $datos['message'] = "Registros añadidos";
            } else {
                //remuevo
                foreach ($req->ids as $key => $value) {
                    # code...
                    $employe = Employes::where('code', '=', $value)->first();
                    $employe->employes_process()->wherePivot('created_at', '=', Carbon::parse($fecha2)->format('Y-m-d'))->detach($req->description);
                }
                $datos['message'] = "Registros removidos";
            }

            $datos['icon'] = "success";
            $datos['title'] = "Correcto";
        } catch (\Throwable $th) {
            //throw $th;
            $datos['message'] = $th->getMessage();
            $datos['icon'] = "error";
            $datos['title'] = "Error";
        }

        return response()->json($datos, 200, []);
    }

    public function changeMasive(Request $req){
        $response = array();
        $proceso = json_decode($req->id);
        $contador = 0;
        try {
            //code...
            $employes = Employes::all();

            $x = false;
            foreach ($employes as $k => $v) {
                # code...
                foreach ($v->employes_process()->whereNull('procesos_employe.deleted_at')->get() as $key => $value) {
                    # code...
                    // return $value;
                    if($value->id == $proceso[0]){
                        DB::table('procesos_employe')->where('id','=',$value->pivot->id)->update(array('deleted_at' => DB::raw('NOW()')));
                        $x = true;
                        $contador++;
                    }
                }

                if($x){
                    $v->employes_process()->attach($proceso[1], ['created_at' => Carbon::now()]);
                    $x=false;
                }
            }
            $response["title"]  = "Correcto";
            $response["icon"] = "success";
            // $data["data"] = $employe;
            $response["message"] = $contador;
        } catch (\Throwable $th) {
            //throw $th;
            $response["title"]  = "Error";
            $response["icon"] = "error";
            // $data["data"] = $employe;
            $response["message"] = $th->getMessage();
        }

        return response()->json($response, 200, []);
    }

    public function importProcess(Request $req){
        if ($req->all()) {
            // return $req->all();
            // $this->validate($req, [
            //     'file_xlsx.*' => 'mimes:xlsx'
            // ]);
            switch ($req->option) {
                case 'procesos':
                    # code...
                    $table = "procesos";//tabla de validacion
                    $columna = "id_proceso";//donde se actualizara
                    $columna_validacion = "name";
                    break;

                case 'tipo_empleado':
                    $table = "employe_type";
                    $columna = "id_employe_type";
                    $columna_validacion = "description";
                    break;

                case 'funciones':
                    $table = "funct_area";
                    $columna = "id_function";
                    $columna_validacion = "description";
                    break;

                default:
                    # code...
                    $columna = $req->option;//donde se actualizara
                    $table = "employes";//tabla de validacion
                    $columna_validacion = $req->option;
                    break;
            }

            if ($req->hasfile('file_xlsx')) {
                // echo "hola";
                // $filename = $req->file('file_xlsx');
                // return $filename;

                $import = new EmployesToggle($columna,$table,$columna_validacion);
                $import->import($req->file('file_xlsx'));
                $all_inserted = $import->getImportedSuccessfully();//return data imported successfully
                if(count($import->failures())==0){
                    if(!Auth::user()->hasGroupPermission("updateProcessEmploye") && !Auth::user()->hasGroupPermission("updateEmploye")){
                        // return abort('pages.403');
                        return response()->view('pages.403', [], 403);
                    }
                    // Session::flash('success', "Todos los registros fueron importados correctamente");
                    return redirect()->back()->with('success','"Todos los registros fueron importados correctamente"');
                }else{
                    $failures = $import->failures();
                    $errors = $import->errors();
                    return view('excel.failures',compact('failures','errors'));
                    // return ;
                }
            }
        }
    }
}
