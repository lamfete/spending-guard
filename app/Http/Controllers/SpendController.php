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
use App\Category as Category;
use App\User;
use Response;
use DB;


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
        //$spendHeaders = SpendHeader::where('user_id', $id)->get();
        $spendDetails = SpendDetail::where('user_id', $id)->get();

        if(!$spendDetails) {
            return Response::json([
                'error' => [
                    'message' => 'Spend detail does not exist'
                ]
            ], 404);
        }

        //return $spendHeaders;
        return Response::json([
            'data' => $this->transformCollectionSpendDetail($spendDetails)
        ], 200);
    }

    public function store(Request $request) {
        if(!$request->category_id or !$request->amount) {
            return Response::json([
                'error' => [
                    'message' => 'Please provide data correctly'
                ]
            ], 422);
        }

        $spendDetail = SpendDetail::create($request->all());

        $dateOnly = \Carbon\Carbon::now();

        //update spend_headers subtotal
        DB::table('spend_headers')
                    ->where('user_id', $request->user_id)
                    ->where(DB::raw('DATE(`updated_at`)'), $dateOnly->toDateString())
                    ->increment('subtotal', $request->amount);

        return Response::json([
            'message' => "Data created successfully",
            'data' => $this->transformSpendDetail($spendDetail)
        ]);
    }

    private function transformCollectionSpendDetail($spendDetails) {
        return array_map([$this, 'transformSpendDetail'], $spendDetails->toArray());
    }

    private function transformSpendDetail($spendDetail) {
        $categoryName = $this->getCategoryName($spendDetail['category_id']);

        return [
            'spend_category_id' => $spendDetail['category_id'],
            'spend_category_name' => $categoryName['name'],
            'spend_amount' => $spendDetail['amount']
        ];
        //return $categoryName[0]['name'];
    }

    private function transformCollectionCategory($categories) {
        return array_map([$this, 'transformCategory'], $categories->toArray());
    }

    private function getCategoryName($categoryId) {
        $category = Category::where('id', $categoryId)->get();
        return $category[0];
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
        $SpendDetails = SpendDetail::where('user_id', $spendHeaderId)->get();  
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
