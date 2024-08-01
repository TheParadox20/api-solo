<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Games;
use App\CustomTypes\GameType;
use App\Models\Bots;
use App\Models\User;
use App\Models\Bets;

class BotsController extends Controller
{
    /** 
     * Get all bots
    */
    public function index(Request $request){
        $bots = Bots::all();
        $users = [];
        $funds = 0;
        foreach($bots as $bot){
            $user = User::find($bot->user_id);
            $users[] = [
                'name' => $user->name,
                'phone' => $user->phone,
                'balance' => $user->balance,
            ];
            $funds+=$user->balance;
        }
        return response()->json([
            'users'=>$users,
            'funds'=>$funds
        ]);
    }
    /** 
     * Get all bets
    */
    public function bets(Request $request){
        $bots = Bots::all();
        $bets = [];
        $exposure = 0;
        $pending = 0;
        $loss = 0;
        $profit = 0;
        foreach($bots as $bot){
            $bet = Bets::where('user_id',$bot->user_id);
            $game = \GameType::fromID($bet->game_id);
            $bets[] = [
                'game' => $game->getName(),
                'stake' => $bet->amount,
                'status' => $bet->status,
                'result' => $bet->result,
                'date' => $game->date,
                'time' => $game->time,
            ];
            if($bet->status==null){
                $exposure+=$bet->amount;
                $pending+=1;
            }
            if($bet->result) $profit+=$bet->amount;
            if(!$bet->result) $loss+=$bet->amount;
        }
        return response()->json([
            'bets'=>$bets,
            'exposure'=>$exposure,
            'pending'=>$pending,
            'loss'=>$loss,
            'profit'=>$profit,
        ]);
    }
    public function create(Request $request){
        try{
            $quantity = $request->quantity;
            if($quantity<=1){
                $user = Bots::create();
                return response()->json([ 
                    'message'=>'success',
                    'name'=>$user->name,
                    'phone'=>$user->phone
                ]);
            }
            if($quantity>1){
                $users = [];
                for ($i=0; $i < $quantity; $i++) { 
                    try{
                        $user = Bots::create();
                        $users[] = [
                            'name'=>$user->name,
                            'phone'=>$user->phone
                        ];
                    } catch (\Exception $e) {
                        Log::info('Error creating bot');
                        $i--;
                    }
                }
                return response()->json([ 
                    'message'=>'success',
                    'users'=>$users,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }
}
