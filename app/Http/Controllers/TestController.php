<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

use App\Models\Books;
use App\CustomTypes\GameType;
use App\Models\Games;
use App\Models\Categories;
use App\Models\Sports;

class TestController extends Controller
{
    public function books()
    {
        $books = Books::all();
        return response()->json($books);
    }
    public function betika(Request $request)
    {
        $client = new Client();
        $sportsURL = 'https://api.betika.com/v1/sports';
        $response = $client->request('GET', $sportsURL);
        $body = $response->getBody();
        $content = json_decode($body->getContents());
        foreach($content->data as $sport){//fetch sports
            $sport_id = $sport->sport_id;
            Sports::create($sport->sport_name);
            foreach($sport->categories as $category){
                foreach($category->competitions as $competition){
                    Categories::create($competition->competition_name, Sports::getID($sport->sport_name=='Soccer'?'Football':$sport->sport_name));
                }
            }
            $periods = [-1];
            // $periods = [-1,1,2,3,4,5,6,7];
            foreach($periods as $period){//fetch matches
                $sport_url = "https://api.betika.com/v1/uo/matches?period_id=" . $period . "&sport_id=" . $sport_id;
                $response = $client->request('GET', $sport_url);
                $body = $response->getBody();
                $matches = json_decode($body->getContents());
                foreach($matches->data as $match){// loop through indiviadual games
                    try{
                        $outcomes = [
                            ['name'=>$match->home_team,'stake'=>0.0,'users'=>0,'odd'=>$match->home_odd],
                            ['name'=>'draw','stake'=>0.0,'users'=>0,'odd'=>$match->neutral_odd],
                            ['name'=>$match->away_team,'stake'=>0.0,'users'=>0,'odd'=>$match->away_odd]
                        ];
                        $game = new \GameType(
                            Sports::getID($sport->sport_name=='Soccer'?'Football':$sport->sport_name),
                            Categories::getID($match->competition_name),
                            $match->start_time,
                            [$match->home_team,$match->away_team],
                            $outcomes
                        );
                        Games::create($game);
                    } catch(\Exception $e){
                        Log::info($e->getMessage());
                    }
                    // break;
                }
            }
        }
        return response()->json([
            'message'=> 'done'
        ]);
    }
}
