<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bets;
use App\Models\Games;
use App\CustomTypes\GameType;

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

    /**
     * Given game_id and choice settle all onchain bets offchain
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function settle(Request $request){
        $Game = \GameType::fromID($request->id);
        $choice = null;
        if($request->choice == 1) $choice = 0; //home
        if($request->choice == 0) $choice = 1; //draw
        if($request->choice == 2) $choice = 2; //away
        throw_if($choice===null, \Exception::class, 'Invalid choice :: '.$choice);
        $stakers = Bets::where('game_id',$request->id)
        ->where('onchain', true)
        ->where('status',null)
        ->where('choice',$request->choice)
        ->get();
        foreach($stakers as $staker){
            $payout = $Game->getPayout($choice, $staker->amount);
            $staker->reward = $payout;
            $staker->status = true;
            $staker->result = false;
            $staker->save();
        }
        return response()->json([
            'message' => 'Onchain bets settled'
        ]);
    }
    public function connect(Request $request){
        $user = $request->user();
        //TODO: Check if wallet exists
        //TODO: Check if user has a wallet
        $user->wallet = $request->wallet;
        $user->save();
        return response()->json([
            'message'=>'Wallet connected'
        ]);
    }
}
