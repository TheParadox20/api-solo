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
    public $id = 0;

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
     * Function that generates a unique id for the game
     */
    public function generateID(){
        $this->gameID = md5($this->options[0] . $this->options[1] . $this->date . $this->time);
    }
    /**
     * Function that sets ID
     */
    public function setID($id){
        $this->gameID =$id;
    }

    /**
     * Function that sets ID
     */
    public function setRow($id){
        $this->id =$id;
    }

    public function setStakes($stakes){
        $this->stakes =$stakes;
    }

    public function setStakers($users){
        $this->users =$users;
    }
    public function getName(){
        return "{$this->options[0]} vs {$this->options[1]}";
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
        $Game = new GameType(
            $game->sport_id,
            $game->category_id,
            $game->start_time,
            $game->options,
            $game->outcomes,
        );
        $Game->setID($game->gameID);
        $Game->setRow($id);
        $Game->setStakers($game->stakers);
        $Game->setStakes($game->amount);
        return $Game;
    }

    /**
     * function to modify a record on placing a bet
     */
    public function update($choice, $amount){
        $game = Games::find($this->id);
        $outcomes = json_decode($game->outcomes);
        $outcome = $outcomes[$choice];
        // update outcome
        $outcome->stake = $outcome->stake+$amount;
        $outcome->users = $amount>0?$outcome->users+1:$outcome->users-1;
        $outcomes[$choice] = $outcome;
        $game->outcomes = json_encode($outcomes);
        $game->stakers = $amount>0?$game->stakers+1:$game->stakers-1;
        $game->amount = $game->amount + $amount;
        //update popularity
        $game->popularity = $game->popularity + $amount;
        $game->save();
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
            'id' => $this->id
        ];
    }
}