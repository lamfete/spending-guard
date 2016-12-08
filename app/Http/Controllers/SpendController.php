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

    public function index(Request $request) {
    	$searchTerm = $request->input('search');
        $limit = $request->input('limit')?$request->input('limit'):5;

        if($searchTerm){
            $spendDetails = SpendDetail::where('user_id', 2) //MASIH DI HARDCODE
            ->where('body', 'LIKE', "%$searchTerm%")
            ->paginate($limit);

            $spendDetails->appends(array(
                'search' => $searchTerm,
                'limit' => $limit
            ));
        }
        else {
            $spendDetails = SpendDetail::where('user_id', 2)->paginate($limit); //MASIH DI HARDCODE

            $spendDetails->appends(array(
                'limit' => $limit
            ));
        }

        return Response::json($this->transformCollectionSpendDetailToIndex($spendDetails), 200);
    }

    public function show($id) {
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
        if(!$request->category_id or !$request->body or !$request->amount or !$request->date) {
            return Response::json([
                'error' => [
                    'message' => 'Please provide data correctly'
                ]
            ], 422);
        }

        $spendDetail = SpendDetail::create($request->all());

        $dateOnly = \Carbon\Carbon::now();

        $spendHeader = SpendHeader::where('created_at', $dateOnly)->first();

        if($spendHeader) {
            $spendHeaderInsert = new SpendHeader;
            $spendHeaderInsert->user_id = $request->user_id;
            $spendHeaderInsert->subtotal = $request->amount;
            $spendHeaderInsert->save();
        }
        else {
            //update spend_headers subtotal
            DB::table('spend_headers')
                        ->where('user_id', $request->user_id)
                        ->where(DB::raw('DATE(`created_at`)'), $request->date)//$dateOnly->toDateString())
                        /*->update([
                            // 'subtotal' => DB::raw('subtotal + ' $request->amount), 
                            'updated_at' => $dateOnly
                        ])*/
                        ->increment('subtotal', $request->amount, ['updated_at' => \Carbon\Carbon::now()]);
        }

        return Response::json([
            'message' => "Data created successfully",
            'data' => $this->transformSpendDetail($spendDetail)
        ]);
    }

    public function update(Request $request, $id) {
        if(!$request->category_id or !$request->body or !$request->amount or !$request->date) {
            return Response::json([
                'error' => [
                    'message' => 'Please provide data correctly'
                ]
            ], 422);
        }

        $spendDetail = SpendDetail::find($id);
        
        /*
        * get amount first, then substract from the subtotal
        * update spend_headers
        *
        */
        $amountDecrement = $spendDetail->amount;

        //update spend_headers subtotal
        DB::table('spend_headers')
                    ->where('user_id', $request->user_id)
                    ->where(DB::raw('DATE(`created_at`)'), $request->date)
                    ->decrement('subtotal', $amountDecrement);

        /*
        * update amount in spend_details
        * update subtotal in spend_headers with updated amount
        *
        */        
        $spendDetail->body = $request->body;
        $spendDetail->amount = $request->amount;
        $spendDetail->category_id = $request->category_id;
        $spendDetail->save();

        //update spend_headers subtotal
        DB::table('spend_headers')
                    ->where('user_id', $request->user_id)
                    ->where(DB::raw('DATE(`created_at`)'), $request->date)
                    ->increment('subtotal', $request->amount, ['updated_at' => \Carbon\Carbon::now()]);

        return Response::json([
            'message' => 'Spend Detail updated successfully'
        ]);
    }

    public function destroy(Request $request, $id) {
        $spendDetail = SpendDetail::find($id);
        
        /*
        * get amount first, then substract from the subtotal
        * update spend_headers
        *
        */
        $amountDecrement = $spendDetail->amount;

        //update spend_headers subtotal
        DB::table('spend_headers')
                    ->where('user_id', $request->user_id)
                    ->where(DB::raw('DATE(`created_at`)'), $request->date)
                    ->decrement('subtotal', $amountDecrement, ['updated_at' => \Carbon\Carbon::now()]);

        spendDetail::destroy($id);

        return Response::json([
            'message' => 'Spend Detail deleted successfully'
        ]);
    }

    private function transformCollectionSpendDetail($spendDetails) {
        return array_map([$this, 'transformSpendDetail'], $spendDetails->toArray());
    }

    private function transformCollectionSpendDetailToIndex($spendDetails) {
        // return array_map([$this, 'transformSpendDetail'], $spendDetails->toArray());
        $spendDetailsArr = $spendDetails->toArray();

        return [
            'total' => $spendDetailsArr['total'],
            'per_page' => intval($spendDetailsArr['per_page']),
            'current_page' => $spendDetailsArr['current_page'],
            'last_page' => $spendDetailsArr['last_page'],
            'next_page_url' => $spendDetailsArr['next_page_url'],
            'prev_page_url' => $spendDetailsArr['prev_page_url'],
            'from' => $spendDetailsArr['from'],
            'to' => $spendDetailsArr['to'],
            'data' => array_map([$this, 'transformSpendDetail'], $spendDetailsArr['data'])
        ];
    }

    private function transformSpendDetail($spendDetail) {
        $categoryName = $this->getCategoryName($spendDetail['category_id']);

        return [
            'spend_detail_id' => $spendDetail['id'],
            'spend_category_id' => $spendDetail['category_id'],
            'spend_category_name' => $categoryName['name'],
            'spend_body' => $spendDetail['body'],
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
}
