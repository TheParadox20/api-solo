<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;

class Web3Controller extends Controller
{
    /**
     * Used to check if a user exists
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        $user = User::where('wallet',$request->wallet)->first();
        return response()->json([
            'active'=>$user==null?false:true,
        ]);
    }
    public function getLatestBlock(Request $request){
    }
}
