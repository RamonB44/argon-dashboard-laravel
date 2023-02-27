<?php

namespace App\Http\Controllers\API;

use App\Area;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\QueryFilter\QueryHistory;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Asistencia;

use function GuzzleHttp\json_decode;

class UserController extends Controller
{
public $successStatus = 200;
/**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){

        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }

    }
/**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

		if ($validator->fails()) {
		    return response()->json(['error'=>$validator->errors()], 401);
		}
		$input = $request->all();
				        $input['password'] = bcrypt($input['password']);
				        $user = User::create($input);
				        $success['token'] =  $user->createToken('MyApp')->accessToken;
				        $success['name'] =  $user->name;

		return response()->json(['success'=>$success], $this-> successStatus);
	}

	 /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this-> successStatus);
    }

    public function setQuery(Request $req){
        // return response()->json($req->all(), 200, ['Accept' => 'application/json']);
        // return $req->all();
        // foreach ($req->all() as $key => $value) {
        //     # code...
        //     return $value['bindings'];
        // }
        $response = array();
        try {
            //code...
            //be carefull this is infinite bucle
            // $response['resultado'] = QueryHistory::insert($req->all());

            foreach ($req->all() as $key => $value) {
                # code...
                if($value["sync_id"]==null){
                    //ingresar nuevo y asisgnar id local
                    $llave_local = $value["id"]."-".Auth::user()->id."-L";
                    // $response["updateOrCreate"][$key]  = 
                    Asistencia::withTrashed()->updateOrCreate(
                        ["sync_id"=> $llave_local],
                        [
                        "code" => $value["code"] ,
                        "temperature"=>$value["temperature"] ,
                        "id_employe" => $value["id_employe"] ,
                        "id_function" => $value["id_function"] ,
                        "id_sede" => $value["id_sede"] ,
                        "id_aux_treg" => $value["id_aux_treg"],
                        "synchronized_users" => "[]",
                        "checked"=> $value["checked"],
                        "created_at"=>$value["created_at"],
                        "updated_at" => $value["updated_at"],
                        "deleted_at"=>$value["deleted_at"],
                        "deletedAt"=>$value["deletedAt"]
                        ]
                    );
                }else{
                    $primary = explode("-",$value["sync_id"]);
                    Asistencia::withTrashed()->updateOrCreate(
                        ["id"=> $primary[0]],
                        [
                        "sync_id" => $value["sync_id"],
                        "code" => $value["code"] ,
                        "temperature"=>$value["temperature"] ,
                        "id_employe" => $value["id_employe"] ,
                        "id_function" => $value["id_function"] ,
                        "id_sede" => $value["id_sede"] ,
                        "id_aux_treg" => $value["id_aux_treg"],
                        "synchronized_users" => "[]",
                        "checked"=> $value["checked"],
                        "created_at"=>$value["created_at"],
                        "updated_at" => $value["updated_at"],
                        "deleted_at"=>$value["deleted_at"],
                        "deletedAt"=>$value["deletedAt"]
                        ]
                    );
                }
                // $response[$key] = $value["sync_id"];
            }
        return response()->json($this->successStatus, 200, ['Content-Type' => 'application/json']);
        } catch (\Throwable $th) {
            //throw $th;
            $response["message"] = $th->getMessage();
            // $response["code"] = $th->getCode();
            return response()->json($response, 500, ['Content-Type' => 'application/json']);
            // $th->getMessage();
        }
        return response()->json($response, 200, ['Content-Type' => 'application/json']);
        //procedimiento para ejecutar la query
    }


}
