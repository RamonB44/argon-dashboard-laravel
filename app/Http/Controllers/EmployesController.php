<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use App\Models\Employes;
use App\Excel\EmployesImport;
use App\Models\Order;
use App\Models\Procesos;
use App\Models\Sedes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class EmployesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    //
    public function index()
    {
        if(!Auth::user()->hasGroupPermission("viewEmployes")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        $area = Area::all();
        $sedes = Sedes::all();
        $proceso = Procesos::all();
        return view('employes.index', compact('area', 'proceso', 'sedes'));
    }

    public function create(Request $req)
    {
        $data = array();
        $headers = array();
        if ($req->all()) {
            if(!Auth::user()->hasGroupPermission("createEmployes")){
                // return abort('pages.403');
                // return response()->view('pages.403', [], 403);
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }

            try {
                //code...

                Employes::withTrashed()->UpdateOrCreate([
                    'id' => $req->code
                ]
                ,[
                    'code' => $req->code,
                    'type' => $req->type,
                    //  => $row["t_emp"]
                    'dir_ind' => $req->dir_ind,
                    'id_employe_type' => $req->t_emp,
                    'valid' => 0,
                    'doc_num' => $req->docnum,
                    'fullname' => $req->fullname,
                    // 'gerencia' => $row["gerencia"],
                    // 'area' => $row["area"],
                    'telephone_num' => $req->telephone,
                    'hasChildren' => $req->hasChildren,
                    'id_function' => $req->funcion,
                    'id_proceso' => $req->proceso,
                    'id_sede' => $req->sede,
                    'turno' => $req->turno,
                    'remuneracion' => $req->remu,
                    'c_costo' => $req->c_costo,
                    'deleted_at' => null//dar de baja
                ]);

                // $data["success"] = true;
                $data["title"]  = "Correcto";
                $data["icon"] = "success";
                // $data["data"] = $employe;
                $data["message"] = "Registrado correctamente";
                // return redirect()->route('employes.index');
            } catch (\Throwable $th) {
                //throw $th;
                $data["title"]  = "Error";
                $data["icon"] = "error";
                // $data["success"] = false;
                $data["message"] = $th->getMessage();
            }
            return response()->json($data, 200, $headers);
        }
    }

    public function update(Request $req, $id)
    {
        $data = array();
        $headers = array();

        try {
            //code...
            $datos = Employes::findOrFail($id);
            $datos->funcion->areas;

            if ($req->all()) {
                if(!Auth::user()->hasGroupPermission("updateEmployes")){
                    // return abort('pages.403');
                    // return response()->view('pages.403', [], 403);
                    $data["success"] = false;
                    $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    $data["icon"] = "warning";
                    $data["title"] = "Authorization Denied";
                    return response()->json($data, 200, []);
                }
                $datos->id = $req->code;
                $datos->code = $req->code;
                $datos->doc_num = $req->docnum;
                $datos->fullname = $req->fullname;
                $datos->id_proceso = $req->proceso;
                $datos->id_function = $req->funcion;
                $datos->c_costo = $req->c_costo;
                //
                $datos->id_employe_type = $req->t_emp;
                //
                $datos->hasChildren = $req->hasChildren;
                $datos->telephone_num = $req->telephone;
                $datos->type = $req->type;
                $datos->id_sede = $req->sede;
                $datos->turno = $req->turno;
                $datos->dir_ind = $req->dir_ind;
                $datos->remuneracion = $req->remu;
                $datos->save();

                $data["title"]  = "Correcto";
                $data["icon"] = "success";
                // $data["data"] = $employe;
                $data["message"] = "Actualizado correctamente";
                // return redirect()->route('employes.index');
                // return $data;
            } else {
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Consultado Correcto";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["title"]  = "Error";
            $data["icon"] = "error";
            // $data["data"] = $employe;
            $data["message"] = $th->getMessage();
        }
        return response()->json($data, 200, $headers);
    }

    public function delete($id)
    {
        $data = array();
        $headers = array();
        try {
            if(!Auth::user()->hasGroupPermission("deleteEmployes")){
                // return abort('pages.403');
                // return response()->view('pages.403', [], 403);
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            //code...
            if (is_array($id)) {
            } else {
                Employes::findOrFail($id)->forceDelete();
                $data["title"]  = "Correcto";
                $data["icon"] = "success";
                // $data["data"] = $employe;
                $data["message"] = "Eliminado correctamente";
            }
        } catch (\Throwable $th) {
            //throw $th;
            // $data["success"] = false;
            $data["title"]  = "Error";
            $data["icon"] = "error";
            $data["message"] = $th->getMessage();
        }
        return response()->json($data, 200, $headers);
    }

    /*public function generateNewCode($id)
    {
        $data = array();
        $headers = array();
        try {
            if(!Auth::user()->hasGroupPermission("updateEmployes")){
                // return abort('pages.403');
                // return response()->view('pages.403', [], 403);
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            //code...
            $datos = Employes::where('id', '=', $id)->first();
            if ($datos->valid == 9) {
                $datos->valid = 1;
            } else {
                $datos->valid = $datos->valid + 1;
            }
            // $data["success"] = true;
            // $data["data"] = $datos;
            // $data["message"] = "Actualizado codigo de validacion";
            $data["title"]  = "Correcto";
            $data["icon"] = "success";
            // $data["data"] = $employe;
            $data["message"] = "Actualizado codigo de validacion";
            $datos->save();
        } catch (\Throwable $th) {
            //throw $th;
            $data["title"]  = "Error";
            $data["icon"] = "error";
            // $data["data"] = $employe;
            $data["message"] = $th->getMessage();
        }
        return response()->json($data, 200, $headers);
    }*/

    public function getTable()
    {
        $data = array();
        $headers = array();
        $config = Auth::user()->getConfig();

        $datos = Employes::whereIn('id_sede', $config['sedes'])->orderBy('deleted_at')->get();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            if (!$value->deleted_at) {
                //$buttons .= "<a class='btn btn-dark m-1 border border-white' href='javascript:generateNew(" . $value->id . ")'><i class='fas fa-redo'></i><a>";
                //$buttons .= "<a class='btn btn-primary m-1 border border-white' href='javascript:showProcedures(" . $value->id . ")'><i class='fas fa-eye'></i><a>";
                $buttons .= "<a class='btn btn-warning m-1 border border-white' href='javascript:edit(" . $value->id . ")'><i class='fas fa-edit'></i><a>";
                $buttons .= "<a class='btn btn-danger m-1 border border-white' href='javascript:eliminar(" . $value->id . ")'><i class='fas fa-trash'></i><a>";
            } else {
                $buttons .= "<h3>Sin Acciones</h3>";
            }

            $data['data'][$key] = [
                $key + 1,
                $value->code,
                // $value->valid,
                $value->doc_num,
                $value->fullname,
                (empty($value->funcion->areas) ? "SIN AREA" : $value->funcion->areas->area),
                (empty($value->funcion) ? "SIN FUNCION" : $value->funcion->description),
                (count($value->employes_process()->whereNull('procesos_employe.deleted_at')->get())>0)?
                ($value->employes_process()->whereNull('procesos_employe.deleted_at')->first()->name):
                $value->procesos->name,
                $value->sedes->name,
                $value->c_costo,
                "<div class='d-flex justify-content-start'>" . $buttons . "</div>",
                ($value->deleted_at) ? true : false
            ];
            // return ;
        }
        return response()->json($data, 200, $headers);
    }

    public function getEmploye($string)
    {
        $data = array();
        $headers = array();
        $today = Carbon::today();
        //$count = strlen($string);
        // return substr($string,-1);
        if (strlen($string) > 0) {

            $datos = Employes::where(DB::raw('concat(code , valid)'), '=', $string)->orWhere('doc_num', '=', $string)->first();

            if ($datos) {
                $order = Order::where('id_employe', '=', $datos->id)->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '=', $today->format('Y-m-d'))->first();
                if (isset($order->detail)) {
                    $order->detail;
                    foreach ($order->detail as $key => $value) {
                        # code...
                        $value->products;
                    }
                }
                $data["order"] = $order;
                $data["success"] = true;
                $data["data"] = $datos;
            } else {
                $data["success"] = false;
            }
        }


        return response()->json($data, 200, $headers);
    }

    /*-public function printBarcode(Request $req)
    {
        $print = PrintController::getInstance();

        try {
            //code...
            $print->printBarcode($req->ids);
            $response["title"]  = "Imprimiendo :)";
            $response["icon"] = "info";
            // $data["data"] = $employe;
            $response["message"] = "Imprimiendo...";
        } catch (\Throwable $th) {
            //throw $th;
            $response["title"]  = "Error";
            $response["icon"] = "error";
            // $data["data"] = $employe;
            $response["message"] = $th->getMessage();
        }
        return response()->json($response, 200, []);
    }*/

    public function importEmploye(Request $req)
    {
        // $req->hasFile('file');
        // return $req->all();
        if ($req->all()) {
            // return $req->all();
            // $this->validate($req, [
            //     'file_xlsx.*' => 'mimes:xlsx'
            // ]);

            if ($req->hasfile('file_xlsx')) {
                // echo "hola";
                // $filename = $req->file('file_xlsx');
                // return $filename;

                // Excel::import(new EmployesImport, $req->file('file_xlsx'));
                $import = new EmployesImport();
                $import->import($req->file('file_xlsx'));

                if(!Auth::user()->hasGroupPermission("createEmployes") && !Auth::user()->hasGroupPermission("updateEmployes")){
                    // return abort('pages.403');
                    return response()->view('pages.403', [], 403);
                    // $data["success"] = false;
                    // $data["message"] = "No tienes los permisos sufienctes para esta accion";
                    // $data["icon"] = "warning";
                    // $data["title"] = "Authorization Denied";
                    // return response()->json($data, 200, []);
                }
                // $all_inserted = $import->getImportedSuccessfully();//return data imported successfully
                if(count($import->failures())==0){
                    // Session::flash('success', "Todos los registros fueron importados correctamente");
                    return redirect()->back()->with('success',"Todos los registros fueron importados correctamente");
                }else{
                    $failures = $import->failures();
                    $errors = $import->errors();
                    return view('excel.failures',compact('failures','errors'));
                    // return ;
                }
            }else{
                return "No hay archivo";
            }
        }
    }

    public function delete_chunck(Request $req)
    {
        // return collect($req->ids);
        $response = array();
        try {
            if(!Auth::user()->hasGroupPermission("updateEmployes")){
                // return abort('pages.403');
                // return response()->view('pages.403', [], 403);
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            //code...
            $datos = Employes::whereIn('code', collect($req->ids))->get();
            $deletes = Employes::whereIn('code', collect($req->ids))->delete();
            // return $deletes;exit;
            if ($deletes > 0) {
                $response["title"]  = "Correcto";
                $response["icon"] = "success";
                // $data["data"] = $employe;
                $response["message"] = "Cesados correctamente";
                // $response['message'] = view('employes.message',compact('deletes','datos'))->render();
            } else {
                $response["title"]  = "Informacion";
                $response["icon"] = "info";
                // $data["data"] = $employe;
                $response["message"] = "No se encontraron registros";
                // $response['message'] = view('employes.message',compact('deletes','datos'))->render();
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["title"]  = "Error";
            $data["icon"] = "error";
            $data["message"] = $th->getMessage();
        }


        return response()->json($response, 200, []);
    }

    public function restore_chunck(Request $req)
    {
        $response = array();

        // return $deletes;exit;
        try {
            if(!Auth::user()->hasGroupPermission("updateEmployes")){
                // return abort('pages.403');
                // return response()->view('pages.403', [], 403);
                $data["success"] = false;
                $data["message"] = "No tienes los permisos sufienctes para esta accion";
                $data["icon"] = "warning";
                $data["title"] = "Authorization Denied";
                return response()->json($data, 200, []);
            }
            $datos = Employes::whereIn('code', collect($req->ids))->get();
            $deletes = Employes::whereIn('code', collect($req->ids))->restore();
            //code...
            if ($deletes > 0) {
                // $response['data'] = $deletes;
                $response["title"]  = "Correcto";
                $response["icon"] = "success";
                // $data["data"] = $employe;
                $response["message"] = "Restaurados correctamente";
                // $response['success'] = true;
                // $response['message'] = view('employes.message',compact('deletes','datos'))->render();
            } else {
                // $response['data'] = $deletes;
                $response["title"]  = "Informacion";
                $response["icon"] = "info";
                // $data["data"] = $employe;
                $response["message"] = "No se encontraron registros";

                // $response['message'] = view('employes.message',compact('deletes','datos'))->render();
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["title"]  = "Error";
            $data["icon"] = "error";
            $data["message"] = $th->getMessage();
        }

        return response()->json($response, 200, []);
    }

    public function massive($id)
    {
        $data = array();
        $headers = array();
        $datos = array();
        if ($id != 0) {
            $datos = Employes::whereHas('funcion', function ($query) use ($id) {
                $query->where('id_area', '=', $id);
            })->get();
        } else {
            $datos = Employes::all();
        }
        // $datos = Employes::where('id_area','=',$id)->get();

        foreach ($datos as $key => $value) {
            # code..

            $data['data'][$key] = [
                $key + 1,
                $value->code,
                $value->doc_num,
                $value->fullname,
                $value->funcion->areas->area,
                $value->procesos->name
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function group(Request $req)
    {
        $data = array();
        $headers = array();

        $datos = Employes::whereIn('code', collect($req->ids))->get();

        foreach ($datos as $key => $value) {
            # code...
            $asistencia = Asistencia::where('id_employe', '=', $value->id)->count();
            $data['data'][$key] = [
                $key + 1,
                $value->code,
                $value->fullname,
                $asistencia
            ];
        }

        return response()->json($data, 200, $headers);
    }

    public function process(Request $req)
    {
        $data = array();
        $headers = array();

        $datos = Employes::whereIn('code', collect($req->ids))->get();

        foreach ($datos as $key => $value) {
            # code...
            $data['data'][$key] = [
                $key + 1,
                $value->code,
                $value->fullname,
                $value->employes_process->count()
            ];
        }

        return response()->json($data, 200, $headers);
    }

    public function reportes()
    {
        if(!Auth::user()->hasGroupPermission("viewREmployes")){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        return view('employes.reportes.index');
    }
}
