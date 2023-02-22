<?php
// use Illuminate\Support\Facades\Auth;

namespace App\QueryFilter;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Session;

// use Symfony\Component\HttpFoundation\Request;
// use Request;

class QueryFilter {
    // private static string $query;

    private static $current;
    public $table_name;

    // private $http = null;

    public static function getInstance()
    {
        if (!self::$current instanceof self) {
            self::$current = new self();
        }

        return self::$current;
    }

    public function __construct()
    {
        $this->http = new GuzzleClient();
        $this->table_name = DB::select('SHOW TABLES');
    }

    public function saveQueries($query){

        $arrayQuery = $this->getTypeQuery($query);
        // dd($arrayQuery);
        $this->setQueries($arrayQuery);
    }
    //returns @string TypeQuery ["insert","update',"select","delete"] or false
    private function getTypeQuery($query){
        // $type = "";
        // return $query;
        if(count($query)>0){
            $arrayQuery = array();
            foreach ($query as $key => $value) {
                # code...
                // dd($value);
                // dd($value['query']);
                $querySplit = explode(" ",str_replace("`","",$value['query']));
                if($this->checkTypeQuery($querySplit[0])){
                    $arrayQuery[$key]["type"] = $querySplit[0];//type query
                    $arrayQuery[$key]["query"] = str_replace("`","",$value['query']);
                    $arrayQuery[$key]["bindings"] = json_encode($value['bindings']);
                    $arrayQuery[$key]["table_name"] = $this->getTableName($querySplit);
                    $arrayQuery[$key]["from_ip"] = \Request::ip();
                    $arrayQuery[$key]["from_mac"] = substr(exec('getmac'), 0, 17);
                    $arrayQuery[$key]["from_pcname"] = gethostbyaddr(\Request::ip());
                    $arrayQuery[$key]["id_user"] = (isset(Auth::user()->id))?Auth::user()->id:null;
                    $arrayQuery[$key]["synchronized"] = ($this->getConnectionStatus())?true:false;
                }
                 //     'from_ip' => \Request::ip() , 'from_mac' => substr(exec('getmac'), 0, 17), 'from_pcname' => gethostbyaddr(\Request::ip()) , 'id_user' => (isset(Auth::user()->id))?Auth::user()->id:null, 'synchronized' => json_encode(($this->getConnectionStatus())?true:false) ]);
                // dd($tableName);
                // dd($arrayQuery);
            }
            // $request->ip()
            // $request = new Request();

            //we got all data
            return $arrayQuery;

        }
        // return false;
        // return $arrayQuery;
    }

    public function setQueries($arrayQuery){
        // dd();
        if($arrayQuery && isset(Auth::user()->id)){
            QueryHistory::insert($arrayQuery);
        }
        // return true;
        // return $arrayQuery;
    }

    private function getTableName($arrayQuery){

        foreach ($this->table_name as $key => $value) {
                if(array_search($value->Tables_in_devsspac_abm,$arrayQuery)){
                    return $value->Tables_in_devsspac_abm;
                }
        }
        return null;
    }

    private function checkTypeQuery($query){
        return ($query == "select"|| $query == "SHOW")?false:true;
    }

    private function getConnectionStatus(){
        $connected = @fsockopen("www.google.com.pe", 80);
                                            //website, port  (try 80 or 443)
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;

    }

    private function getAccessToken(){
        //route('passport.token')
        // dd("ok");

        try {
            //code...
            $response = $this->http->post("demo-abm.devs-space.com/api/login", [
                'form_params' => [
                    'username' => "admin@admin.com",
                    'password' => "password",
                ],
            ]);

            Session::put('token_access', json_decode((string) $response->getBody(), true)["access_token"]);
            Session::save();
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }
    }

    private function refreshToken(){

    }

    public function setQueriesInServer($arrayQuery){

        // dd();
        if(isset(Auth::user()->id)){

            // dd(arrayQuery);
            // return $result; generate new access token if no exist
            if($this->getConnectionStatus()){
            //     //send data by api to server

                if(Session::get('token_access')==null || !Session::has('token_access')){
                    $this->getAccessToken();
                }
                // dd(Session::get('token_access'));
                try {
                    //code...
                    // Session::forget('token_access');
                    // dd(Session::get('token_access'));
                    //change route for server url
                    $response = $this->http->post("demo-abm.devs-space.com/api/setQuery",[
                        'headers' => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer '. Session::get('token_access')
                        ],
                        // 'json' => $arrayQuery
                        \GuzzleHttp\RequestOptions::JSON => $arrayQuery
                    ]);

                    return $response->getBody()->getContents();
                    // dd($response->getBody()->getContents());
                } catch (\Throwable $th) {
                    //throw $th;
                    // dd($th->getMessage());
                    if($th->getcode()!=200){
                        // $this->newAccessToken = false;
                        // dd("que");
                        Session::forget('token_access');
                        // QueryHistory::insert($arrayQuery);
                        $this->getAccessToken();
                        return response()->json(["code" => $th->getCode()], 200, []);
                    }
                }

            }else{
                // QueryHistory::insert($arrayQuery);
                // QueryHistory::insert(
                //     ['type' => json_encode($arrayQuery['type']),'query' => json_encode($arrayQuery['query']),'table_name' => json_encode($arrayQuery['table_name']),'bindings' => json_encode($arrayQuery['bindings']),
                //     'from_ip' => \Request::ip() , 'from_mac' => substr(exec('getmac'), 0, 17), 'from_pcname' => gethostbyaddr(\Request::ip()) , 'id_user' => (isset(Auth::user()->id))?Auth::user()->id:null, 'synchronized' => json_encode(($this->getConnectionStatus())?true:false) ]);
                return response()->json(["code" => 404], 200, []);
            }
            // return true;
        }else{
            // dd("s");
            return response()->json(["code" => 401], 200, []);
        }
        // return true;
        // return $arrayQuery;
    }

    public function getQueriesfromServer(){
        if($this->getConnectionStatus()){
            //     //send data by api to server
                if(Session::get('token_access')==null || !Session::has('token_access')){
                    $this->getAccessToken();
                }
                // dd(Session::get('token_access'));
                try {
                    //code...
                    // Session::forget('token_access');
                    // dd(Session::get('token_access'));
                    //change route for server url
                    $response = $this->http->get("demo-abm.devs-space.com/api/assistance/checkAsistencia",[
                        'headers' => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer '. Session::get('token_access')
                        ],
                        // 'json' => $arrayQuery
                        // \GuzzleHttp\RequestOptions::JSON => $arrayQuery
                    ]);

                    return $response->getBody()->getContents();
                } catch (\Throwable $th) {
                    //throw $th;
                    // dd($th->getMessage());
                    if($th->getcode()!=200){
                        // $this->newAccessToken = false;
                        // dd("que");

                        Session::forget('token_access');
                        // QueryHistory::insert($arrayQuery);
                        $this->getAccessToken();
                        return response()->json(["code" => $th->getCode()], 200, []);

                    }
                    // return $th->getMessage();
                }

            }
    }
}
