<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomTypes\GameType;
use App\Models\Sports;
use App\Models\Categories;
use App\Models\Games;
use App\Models\Bets;
use App\Models\Live;

class BetsController extends Controller
{
    public function place(Request $request)
    {
        try{
            $user = $request->user();
            throw_if(!$user, \Exception::class, 'User unauthenticated');
            if($request->web3) Bets::record($user->id, $request->game,$request->amount,$request->choice);
            else Bets::create($user->id, $request->game,$request->amount,$request->choice);
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
    public function edit(Request $request)
    {
        try{
            $user = $request->user();
            throw_if(!$user, \Exception::class, 'User unauthenticated');
            return response()->json([
                'message'=>'Bet modified'
            ]);
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }
    public function delete(Request $request)
    {
        try{
            $user = $request->user();
            throw_if(!$user, \Exception::class, 'User unauthenticated');
            $bet = Bets::find($request->id);
            throw_if(!$bet, \Exception::class, 'Bet not found');
            $amount = $bet->amount*-1;
            //update user balance
            $user->balance = $user->balance - $amount;
            $user->save();
            //update game stats
            $Game = \GameType::fromID($bet->game_id);
            $Game->setRow($bet->game_id);
            $Game->update($bet->choice, $amount);
            $bet->delete();
            return response()->json([
                'message'=>'Bet deleted',
                'amount'=>$amount*-1
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
            $bets = [];
            $bets = Bets::where('user_id', $user->id)->where('status', null)->get();
            $bets = $bets->map(function($bet){
                $game = \GameType::fromID($bet->game_id);
                if($bet->choice == 1) $choice = 0; //home
                if($bet->choice == 0) $choice = 1; //draw
                if($bet->choice == 2) $choice = 2; //away
                $game->setRow($bet->game_id);
                $live = Live::where('game_id', $bet->game_id)->first();
                $status = $live?
                [
                    'name' => 'Live',
                    'homeScore' => $live->home_score,
                    'awayScore' => $live->away_score,
                    'time' => $live->time
                ]:
                [
                    'name' => 'Pending',
                    'homeScore' => 0,
                    'awayScore' => 0,
                ];
                return [
                    'id' => $bet->id,
                    'homeTeam' => $game->options[0],
                    'awayTeam' => $game->options[1],
                    'start' => $game->time,
                    'stake' => $bet->amount,
                    'choice' => $game->outcomes[$choice]->name,
                    'payout' => $game->getPayout($choice, $bet->amount),
                    'status' => $status
                ];
            });
            return response()->json($bets);
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }

     /**
      * Get all bets
      * @param \Illuminate\Http\Request $request
      * @return mixed|\Illuminate\Http\JsonResponse
      */
      public function bets(Request $request)
      {
          try{
                $user = $request->user();
                throw_if(!$user, \Exception::class, 'User unauthenticated');
                $bets = Bets::where('user_id', $user->id)->get();
                $bets = $bets->map(function($bet){
                    $game = \GameType::fromID($bet->game_id);
                    if($bet->choice == 1) $choice = 0; //home
                    if($bet->choice == 0) $choice = 1; //draw
                    if($bet->choice == 2) $choice = 2; //away
                    return [
                        'betID' => strtoupper(substr($game->gameID, 0, 5)),
                        'settled' => $bet->status==true?true:false,
                        'market' => $bet->market,
                        'homeTeam' => $game->options[0],
                        'awayTeam' => $game->options[1],
                        'stake' => $bet->amount,
                        'choice' => $game->outcomes[$bet->choice]->name,
                        'payout' => $bet->result==true || $bet->result==null?$game->getPayout($choice, $bet->amount):0,
                        'date'=>date('Y-m-d', strtotime($game->date)),
                    ];
                });
              return response()->json($bets);
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
    /**
     * Given gameID return bet info
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function BetInfo(Request $request){
        try{
            $game = Games::find($request->id);
            $outcomes = json_decode($game->outcomes);
            return response()->json($outcomes);
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }
}
