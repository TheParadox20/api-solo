<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

use App\CustomTypes\GameType;
use App\Models\Games;
use App\Models\Categories;
use App\Models\Sports;
use DateTime;

class Live extends Model
{
    use HasFactory;
    protected $fillable = ['game_id','start_time','time','home_score','away_score','status'];
    public static function create($game){
        $live = new Live();
        $options = [$game['home_team'],$game['away_team']];
        $demo_options = ["Manchester City FC (Leonardo)","Tottenham Hotspur FC (Splinter)"];
        $match = Games::whereJsonContains('options', $options)
                            ->where('start_time', $game['start_time'])
                            ->first();
        throw_if(!$match, \Exception::class, "!! GAME not FOUND {$game['home_team']} ::VS:: {$game['away_team']} on {$game['start_time']} !!");
        $live->game_id = $match->id;
        // given score in the form home_score:away_score
        $score = explode(":", $game['current_score']);
        $live->home_score = $score[0]=='-'?0:$score[0];
        $live->away_score = $score[1]=='-'?0:$score[1];
        $live->start_time = $game['start_time'];
        if ($game['time'] == 'Time Unknown') {
            $current_time = new DateTime();
            $start_time = new DateTime($live->start_time);
            $interval = $current_time->diff($start_time);
            $live->time = $interval->format('%H:%I:%S');
        } else $live->time = $game['time'];
        $live->status = "Live";
        Log::info("{$game['home_team']}-{$live->home_score} ::VS:: {$game['away_team']}-{$live->away_score} on {$game['start_time']} || game_id: {$live->game_id} || current time: {$game['time']}");
        Log::info(str_repeat("-", 50));
        // if game_id exists update else create
        $existing = Live::where('game_id', $live->game_id)->first();
        if ($existing) {
            $existing->home_score = $live->home_score;
            $existing->away_score = $live->away_score;
            $existing->time = $live->time;
            $existing->status = $live->status;
            $existing->save();
            return;
        }
        $live->save();
    }
}
