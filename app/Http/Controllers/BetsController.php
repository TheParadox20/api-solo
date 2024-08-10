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
                'message'=>'Bet placed'
            ]);
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }
     /**
      * Get active bets for a user
      * @param \Illuminate\Http\Request $request
      * @return mixed|\Illuminate\Http\JsonResponse
      */
     public function betslip(Request $request)
    {
        try{
            $user = $request->user();
            throw_if(!$user, \Exception::class, 'User unauthenticated');
            return response()->json([
            ]);
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }
    /**
     * Given a userID, gameID, amount, and outcome place a bet
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function BotsPlace(Request $request)
    {
        try{
            $user = $request->user();
            // TODO only admin
            // throw_if(!$user, \Exception::class, 'User unauthenticated');
            Bets::create($request->user, $request->game,$request->amount,$request->choice);
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
