<?php

namespace App\Http\Controllers\API\Auth;

use App\User;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\API\Auth\IssueTokenTrait;


class LoginController extends Controller
{
	use IssueTokenTrait;
    //
    private $client;

	public function __construct(){
		$this->client = Client::find(2);
	}

	public function login(Request $request){
		$this->validate($request,[
			'username' => 'required|email',
			'password' => 'required'
		]);

		return $this->issueToken($request, 'password');
	}

	public function refresh(Request $request){

		$this->validate($request,[
			'refresh_token' => 'required',
		]);

		return $this->issueToken($request, 'refresh_token');
	}

	public function logout(Request $request){

		$accesstoken = Auth::user()->token();

		DB::table('oauth_refresh_tokens')->where('access_token_id', $accesstoken->id)->update(['revoked'=>true]);

		$accesstoken->revoke();

		return response()->json([],204);
    }

    public function getRole(){
        $user = Auth::user()->role_id;
        return response()->json(['data' => $user], 200);
    }

}
