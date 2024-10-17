<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\CustomTypes\GameType;
use App\Models\Games;
use App\Models\User;

class Bets extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'game_id', 'market', 'choice', 'amount', 'status', 'result','onchain','reward'];
    public static function create($user, $game, $amount, $choice){
        throw_if(Bets::where('game_id', $game)->where('user_id', $user)->first(), \Exception::class, 'Bet exists');
        $user = User::find($user);
        throw_if($user->balance < $amount, \Exception::class, 'Insufficient funds');
        $bet = new Bets();
        $bet->user_id = $user->id;
        $bet->game_id = $game;
        $bet->choice = $choice;
        $bet->amount = $amount;
        //update user balance
        $user->balance -= $amount;
        //update game stats
        $Game = \GameType::fromID($game);
        $Game->setRow($game);
        $Game->update($choice, $amount);
        $bet->save();
        $user->save();
    }
    public static function record($user, $game, $amount, $choice){
        throw_if(Bets::where('game_id', $game)->where('user_id', $user)->first(), \Exception::class, 'Bet exists');
        $user = User::find($user);
        throw_if($user->balance < $amount, \Exception::class, 'Insufficient funds');
        $bet = new Bets();
        $bet->user_id = $user->id;
        $bet->game_id = $game;
        $bet->choice = $choice;
        $bet->amount = $amount;
        $bet->onchain = true;
        //update game stats
        $Game = \GameType::fromID($game);
        $Game->setRow($game);
        $Game->update($choice, $amount);
        $bet->save();
        $user->save();
    }
}
