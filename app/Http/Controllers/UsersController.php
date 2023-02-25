<?php

namespace App\Http\Controllers;

use App\Models\Area;
// use App\ModelsAuxiliar\\TypeReg as AuxiliarTypeReg;
use App\Models\Group;
use App\Models\Auxiliar\TypeReg;
use App\Models\Sedes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function show()
    {

        // dd(Auth::user()->user_group->first()->id != 2);
        if(Auth::user()->user_group->first()->id != 2){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        return view('users.index');
    }

    public function getTable()
    {
        $data = array();
        $headers = array();

        $datos = User::withTrashed()->whereHas('user_group', function ($query) {
            $query->where('id_group', '!=', 2);
        })->get();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            if (!$value->deleted_at) {
                // $buttons .= "<a class='btn btn-dark m-1 border border-white' href='javascript:generateNew(" . $value->id . ")'><i class='fas fa-redo'></i><a>";
                // $buttons .= "<a class='btn btn-primary m-1 border border-white' href='javascript:showProcedures(" . $value->id . ")'><i class='fas fa-eye'></i><a>";
                $buttons .= "<a class='btn btn-warning m-1 border border-white' href='" . route('users.update', ['id' => $value->id]) . "'><i class='fas fa-edit'></i><a>";
                $buttons .= "<a class='btn btn-danger m-1 border border-white' href='javascript:eliminar(" . $value->id . ")'><i class='fas fa-trash'></i><a>";
            } else {
                $buttons .= "<h3>Sin Acciones</h3>";
            }

            $data['data'][$key] = [
                $key + 1,
                $value->username,
                $value->email,
                ($value->deleted_at) ? "Inactivo" : "Activo",
                Carbon::parse($value->created_at)->format('d/m/Y'),
                $buttons
                // $value->fullname,
                // $value->funcion->areas->area,
                // $value->funcion->description,
                // $value->procesos->name,
                // ($value->deleted_at)?"CESADO":"TRABAJANDO",
                // "<div class='d-flex justify-content-start'>".$buttons."</div>",
                // ($value->deleted_at)?true:false
            ];
        }
        return response()->json($data, 200, $headers);
    }

    protected function validator(array $data, $update_id)
    {
        // print_r($update_id);exit;
        // return Validator::make($data, [
        //     'name' => ['required', 'string', 'max:255'],
        //     'lastname' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => ['required', 'string', 'min:8', 'confirmed'],
        //     // 'areas' => ['required'],
        //     // 'treg' => [],
        //     'sedes' => ['required']
        // ]);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            // 'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email' . (($update_id) ? "," . $update_id . ",id" : "")],

            'password' => ($update_id) ? ['confirmed', 'max:16'] : ['required', 'max:16', 'confirmed'],

            // 'areas' => ['required'],
            // 'treg' => [],
            'sedes' => ['required']
        ];
        $messages = [
            'required' => 'Campo requerido',
            'numeric'  => 'Campo Numerico [0,9]',
            'confirmed' => 'El campo de contrase���a no concide',
            'email' => 'No es una direccion valida',
            'unique' => 'El usuario ya esta en uso',
            'id' => 'El usuario ya esta en uso'
        ];
        return Validator::make($data, $rules, $messages);

        // return $validator;
    }

    public function create(Request $req)
    {
        $this->validator($req->all(), null)->validate();
        // redirect()->back()
        $employe = User::create([
            'username' => $req->name,
            'email' => $req->email,
            //'password' => Hash::make($req->password)
            'password' => $req->password
        ]);

        if ($req->group == 2) {
            return redirect()->route('users.create');
        }
        // return $req->all();
        $employe->user_group()->attach([$req->group], array('show_aux_treg' => json_encode($req->treg), 'show_areas' => json_encode($req->areas), 'sedes' => json_encode($req->sedes)));
        $data["success"] = true;
        $data["data"] = $employe;
        $data["message"] = "Registrado Correcto";

        return redirect()->route('users.index');
        // return response()->json($data, 200, $headers);
    }

    public function create_form()
    {
        if(Auth::user()->user_group->first()->id != 2){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        $config = Auth::user()->getConfig();
        $areas = Area::all();
        $treg = TypeReg::all();
        $sedes = Sedes::all();
        $group = Group::where('id', '!=', 2)->get();
        return view('users.create', compact('areas', 'treg', 'config', 'group', 'sedes'));
    }

    public function update(Request $req, $id)
    {
        $data = array();
        $headers = array();

        $config = Auth::user()->getConfig();
        $areas = Area::all();
        $treg = TypeReg::all();
        $sedes = Sedes::all();
        $group = Group::where('id', '!=', 2)->get();

        $user = User::whereHas('user_group', function ($query) {
            $query->where('id_group', '!=', 2);
        })->where('id', '=', $id)->first();

        if (!$user) {
            return redirect()->route('users.index');
        }
        $user->user_group;

        if ($req->all()) {
            $this->validator($req->all(), $user->id)->validate();
            // redirect()->back()
            $user->username = $req->name;
            $user->email = $req->email;
            if ($req->password) {
                $user->password = $req->password;
            }
            $user->save();
            // return $req->all();
            $user->user_group()->sync(array($req->group => array('show_aux_treg' => json_encode($req->treg), 'show_areas' => json_encode($req->areas), 'sedes' => json_encode($req->sedes))));
            $data["success"] = true;
            $data["data"] = $user;
            $data["message"] = "Actualizado Correcto";
            return redirect()->route('users.index');
        } else {
            if(Auth::user()->user_group->first()->id != 2){
                // return abort('pages.403');
                return response()->view('pages.403', [], 403);
            }
            return view('users.edit', compact('areas', 'treg', 'config', 'group', 'sedes', 'user'));
        }

        // return response()->json($data, 200, $headers);
    }

    public function delete($id)
    {
        $data = array();
        $headers = array();
        try {
            if(Auth::user()->user_group->first()->id != 2){
                // return abort('pages.403');
                $data["success"] = true;
                // $data["data"] = $datos;
                $data["message"] = "Permisos Insuficientes";
                return response()->json($data, 200, []);
            }
            //code...
            if (is_array($id)) {
            } else {
                $datos = User::where('id', '=', $id)->delete();
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Eliminado correcto";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
        }
        return response()->json($data, 200, $headers);
    }

    public function config(Request $req)
    {
        $config = Auth::user()->getConfig();
        $areas = Area::all();
        $treg = TypeReg::all();
        $sedes = Sedes::all();
        if ($req->all()) {
            $user = User::where('id', '=', Auth::user()->id)->first();
            $group = $user->user_group->first()->id;
            $user->user_group()->sync(array($group => array('show_aux_treg' => json_encode($req->treg), 'show_areas' => json_encode($req->areas) , 'show_function' => json_encode(($req->funct)?$req->funct:[]))));
            return redirect()->back()->with('success',"Configuracion Actualizada");
        } else {
            return view('users.config', compact('config', 'areas', 'treg'));
        }
    }

    protected function profile_validator(array $data, $update_id)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            // 'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email' . (($update_id) ? "," . $update_id . ",id" : "")],

            'password' => ($update_id) ? ['confirmed', 'max:16'] : ['required', 'max:16', 'confirmed'],

            // 'areas' => ['required'],
            // 'treg' => [],
            // 'sedes' => ['required']
        ];
        $messages = [
            'required' => 'Campo requerido',
            'numeric'  => 'Campo Numerico [0,9]',
            'confirmed' => 'El campo de contrase���a no concide',
            'email' => 'No es una direccion valida',
            'unique' => 'El usuario ya esta en uso',
            'id' => 'El usuario ya esta en uso'
        ];
        return Validator::make($data, $rules, $messages);

        // return $validator;
    }

    public function profile(Request $req)
    {
        if ($req->all()) {

            $this->profile_validator($req->all(), Auth::user()->id)->validate();
            $user = User::where('id', '=', Auth::user()->id)->first();
            $user->name = $req->name;
            $user->email = $req->email;
            if ($req->password) {
                $user->password = Hash::make($req->password);
            }
            $user->save();
            // return $user;
            $data["success"] = true;
            $data["data"] = $user;
            $data["message"] = "Actualizado Correcto";
            // return redirect()->route('users.profile');
            return redirect()->back()->with('success',"Perfil Actualizado");
        } else {
            return view('users.profile');
        }
    }
}
