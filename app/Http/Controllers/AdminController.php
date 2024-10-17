<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bets;
use App\Models\Games;
use App\CustomTypes\GameType;

class AdminController extends Controller
{
    //
    public function getActiveBets(Request $request){
        $bets = Bets::where('status',null)->get('game_id');
        $games = [];
        foreach($bets as $bet){
            $games[] = \GameType::fromID($bet->game_id)->package();
        }
        return response()->json($games);
    }
    public function closeBet(Request $request){}
}
