<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Games extends Model
{
    use HasFactory;
    protected $fillable = ['sport_id', 'category_id','amount','stakers','start_time','options' ,'outcomes','popularity','gameID','results'];
    public static function create($game): Games | null
    {
        $row = new Games();
        $row->sport_id = $game->sport;
        $row->category_id = $game->category;
        $row->amount = 0.0;
        $row->stakers = 0;
        $row->start_time = $game->date . " " . $game->time;
        $row->popularity = 0.0;
        $row->options = json_encode($game->options);
        $row->outcomes = json_encode($game->outcomes);
        $game->generateID();
        if(Games::where('gameID', $game->gameID)->first()) return null;
        $row->gameID = $game->gameID;
        $row->save();
        Log::info("{$game->options[0]} ::VS:: {$game->options[1]} on {$game->date} at {$game->time}");
        return $row;
    }
}
