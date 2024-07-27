<?php

use App\Models\Games;
use App\Models\Sports;
use App\Models\Categories;

class GameType
{
    public $sport = 0;
    public $category = 0;
    public $stakes = 0.0;
    public $users = 0;
    public $date = '';
    public $time = '';
    public $options = ['',''];
    public $outcomes = [array(['name'=>'','stake'=>0.0,'users'=>0,'odd'=>0.0]),array(['name'=>'','stake'=>0.0,'users'=>0,'odd'=>0.0])];
    public $gameID = '';

    function __construct(
        $sport,
        $category,
        $datetime,
        $options,
        $outcomes,
    ){
        $this->sport = $sport;
        $this->category = $category;
        $this->stakes = 0.0;
        $this->users = 0.0;
        $dateTime = new DateTime($datetime);
        $this->date = $dateTime->format('Y-m-d');
        $this->time = $dateTime->format('H:i:s');
        $this->options = $options;
        $this->outcomes = $outcomes;
    }
    /**
     * Static function that generates a unique id for the game
     */
    public function generateID(){
        $id = md5($this->options[0] . $this->options[1] . $this->date . $this->time);
        $this->gameID = substr($id, 0, 5);
    }

    /**
     * Return all the data in json format
     */
    public function toJson(){
        return json_encode($this);
    }

    /**
     * Given the numeric index id for the games table construct the object
     */
    public static function fromID($id){
        $game = Games::find($id);
        Log::info($game);
        return new GameType(
            $game->sport_id,
            $game->category_id,
            $game->start_time,
            $game->options,
            $game->outcomes,
        );
    }

    /**
     * Package the data for client payload
     */
    public function package(){
        return [
            'sport' => Sports::getName($this->sport),
            'category' => Categories::getName($this->category),
            'stakes' => $this->stakes,
            'users' => $this->users,
            'date' => $this->date,
            'time' => $this->time,
            'options' => json_decode($this->options),
            'outcomes' => json_decode($this->outcomes),
            'id' => $this->gameID
        ];
    }
}