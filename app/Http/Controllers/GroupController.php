<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        if(Auth::user()->user_group->first()->id != 2){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        return view('group.index');
    }

    public function getTable(){
        $data = array();
        $headers = array();

        $datos = Group::where('id','!=',2)->get();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            $buttons .= "<a class='btn btn-warning m-1 border border-white' href='".route('group.update',['id'=>$value->id])."'><i class='fas fa-edit'></i><a>";
            $buttons .= "<a class='btn btn-danger m-1 border border-white' href='javascript:eliminar(" . $value->id . ")'><i class='fas fa-trash'></i><a>";

            $data['data'][$key] = [
                $key + 1,
                $value->group_name,
                $buttons
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function create(Request $req){
        if(Auth::user()->user_group->first()->id != 2){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        if ($req->all()) {

            $permission = serialize($req->permission);

            $data = array(
                'group_name' => $req->group_name,
                'permission' => $permission
            );

            $group = new Group($data);
            $group->save();

            return redirect()->route('groups.index');
        } else {
            return view('group.create');
        }
    }

    public function update(Request $req,$id){
        if(Auth::user()->user_group->first()->id != 2){
            // return abort('pages.403');
            return response()->view('pages.403', [], 403);
        }
        if ($req->all()) {
            $permission = serialize($req->permission);

            $group = Group::findOrFail($id);
            $group->permission = $permission;
            $group->group_name = $req->group_name;
            $group->save();

            return redirect()->route('groups.index');
        } else {
            $data = Group::where('id', '=', $id)->first();
            return view('group.edit', compact('data'));
        }
    }

    public function delete($id)
    {
        if(Auth::user()->user_group->first()->id != 2){
            // return abort('pages.403');
            $data["success"] = true;
            // $data["data"] = $datos;
            $data["message"] = "Permisos Insuficientes";
            return response()->json($data, 200, []);
        }
        $data = array();
        $headers = array();
        try {
            //code...
            if (is_array($id)) {
            } else {
                $datos = Group::where('id', '=', $id)->delete();
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

    // public function getData(){
    //     $group = Group::all();
    //     return response()->json($group, 200, []);
    // }
}
