<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use JWTAuth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use App\SpendHeader as SpendHeader;
use App\SpendDetail as SpendDetail;
use App\User;
use Response;


class SpendController extends Controller
{
	public function __construct() {
		$this->middleware('jwt.auth', ['except' => ['authenticate']]);
	}

    public function index() {
    	$spendHeaders = SpendHeader::all();
    	return $spendHeaders;
    }

    public function show($id) {
        $spendHeaders = SpendHeader::where('user_id', $id)->get();

        if(!$spendHeaders) {
            return Response::json([
                'error' => [
                    'message' => 'Spend Header does not exist'
                ]
            ], 404);
        }

        //return $spendHeaders;
        return Response::json([
            'data' => $this->transformCollectionSpendHeader($spendHeaders)
        ], 200);
    }

    private function transformCollectionSpendHeader($spendHeaders) {
        return array_map([$this, 'transformSpendHeader'], $spendHeaders->toArray());
    }

    private function transformSpendHeader($spendHeader) {
        //$SpendDetails = SpendDetail::where('spend_header_id', $spendHeader['id']->get());

        return [
            'spend_header_id' => $spendHeader['id'],
            'spend_header_subtotal' => $spendHeader['subtotal'],
            'spend_header_date' => $spendHeader['created_at'],
            'spend_details_data' => $this->transformSpendDetails($spendHeader['id'])
        ];
    }

    private function transformSpendDetails($spendHeaderId) {
        $SpendDetails = SpendDetail::where('spend_header_id', $spendHeaderId)->get();  
        return $SpendDetails;      
    }

    /*
    public function authenticate(Request $request) {
    	$credentials = $request->only('email', 'password');

    	try {
    		//verify the credentials and create a token for the user
    		if(!$token = JWTAuth::attempt($credentials)) {
    			return response()->json(['error' => 'Unauthorized'], 401);
    		}
    	} catch (JWTException $e) {
    		//something went wrong
    		return response()->json(['error' => 'could_not_create_token'], 500);
    	}

    	//if no errors are encountered we can return a JWT
    	return response()->json(compact('token'));
    }*/
}
