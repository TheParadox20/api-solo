<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class Web3Controller extends Controller
{
    public function index(Request $request){
        $user = User::where('wallet',$request->wallet)->first();
        return response()->json([
            'active'=>$user==null?false:true,
            'user'=>$user
        ]);
    }
}
