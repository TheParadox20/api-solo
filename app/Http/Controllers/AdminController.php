<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bets;
use App\Models\Games;
use App\Models\User;
use App\CustomTypes\GameType;

class AdminController extends Controller
{
    //
    public function getActiveBets(Request $request){
        $bets = Bets::where('status',null)->distinct()->pluck('game_id');
        $games = [];
        foreach($bets as $game_id){
            $games[] = \GameType::fromID($game_id)->package();
        }
        return response()->json($games);
    }
    /**
     * Get possible payouts for a given scenario/outcome
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function payouts(Request $request){
        $Game = \GameType::fromID($request->id);
        $choice = null;
        if($request->choice == 1) $choice = 0; //home
        if($request->choice == 0) $choice = 1; //draw
        if($request->choice == 2) $choice = 2; //away
        throw_if($choice===null, \Exception::class, 'Invalid choice :: '.$choice);
        $stakers = Bets::where('game_id',$request->id)->get();
        $payouts = [];
        foreach($stakers as $staker){
            if($staker->choice == $request->choice){
                $payout = $Game->getPayout($choice, $staker->amount);
                $payouts[] = [
                    'reward'=>$payout,
                    'stake'=>$staker->amount,
                    'odd'=>$payout / $staker->amount
                ];
            }
        }
        return response()->json($payouts);
    }
    public function closeBet(Request $request){
        $Game = \GameType::fromID($request->id);
        $game = Games::find($request->id);
        $outcomes = json_decode($game->outcomes);
        $choice = null;
        if($request->choice == 1) $choice = 0; //home
        if($request->choice == 0) $choice = 1; //draw
        if($request->choice == 2) $choice = 2; //away
        $winnersPot = $outcomes[$choice]->stake;
        $winnings = $game->amount - $winnersPot;
        throw_if($choice===null, \Exception::class, 'Invalid choice :: '.$choice);
        $stakers = Bets::where('game_id',$request->id)
        ->where('status',null)
        ->get();
        foreach($stakers as $staker){
            if($staker->choice == $request->choice){
                $payout = $Game->getPayout($choice, $staker->amount);
                if($staker->onchain==false){
                    $staker->reward = $payout;
                    $staker->status = true;
                    $staker->result = false;
                    $user = User::find($staker->user_id);
                    $user->balance += $payout;
                    $user->save();
                    $staker->save();
                }
            }else{
                $staker->reward = -1 * $staker->amount;
                $staker->status = true;
                $staker->result = false;
                $staker->save();
            }
        }
        return response()->json([
            'message' => 'Offchain bets settled',
            'success'=>true,
            'winnersPot' => $winnersPot,
            'winnings' => $winnings
        ]);
    }
}
