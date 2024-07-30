<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomTypes\GameType;
use App\Models\Sports;
use App\Models\Categories;
use App\Models\Games;
use App\Models\Bets;

class BetsController extends Controller
{
    public function place(Request $request)
    {
        try{
            $user = $request->user();
            throw_if(!$user, \Exception::class, 'User unauthenticated');
            Bets::create($user->id, $request->game,$request->amount,$request->choice);
            return response()->json([
                'message'=>'Succesfuly placed bet'
            ]);
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }
}
