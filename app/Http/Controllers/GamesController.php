<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\CustomTypes\GameType;
use App\Models\Sports;
use App\Models\Categories;
use App\Models\Games;

class GamesController extends Controller
{
  /**
   * List all sports, categories
   * @param \Illuminate\Http\Request $request
   * @return void
   */
  public function index(Request $request)
  {
    $sports = [];
    $stats = [
      'sports'=>Sports::count(),
      'games'=>Games::count(),
    ];
    $Sports = Sports::all();
    foreach($Sports as $sport){
      $sports[] = [
        'name' => $sport->name,
        'id' => $sport->id,
        'categories' => Categories::where('sport_id',$sport->id)->get(['name','id']),
        'games' => Games::where('sport_id',$sport->id)
                          ->orderByDesc('popularity')
                          ->get()
      ];
    }
    return response()->json([
      'sports' => $sports,
      'stats' => $stats,
    ]);
  }
  public function home(Request $request)
  {
      $sports = Sports::orderByDesc('popularity')->take(2)->get(['id','name']);
      $popular = [];
      foreach ($sports as $sport) {
        // games where start_time is greater than now
        $games = Games::where('sport_id',$sport['id'])
                      ->where('start_time','>',date('Y-m-d H:i:s'))
                      ->orderByDesc('popularity')
                      ->orderBy('start_time')
                      ->take(2)
                      ->get(['id']);
        $popular[$sport['name']] = array_map(function($game){
        return \GameType::fromID($game['id'])->package();
        }, $games->toArray());
      }
      return response()->json($popular);
  }
  public function games(Request $request)
  {
      try{
        $games = [];
        $categories = [];
        $path = explode('/',$request->path);
        
        $dates = [];
        $today = new \DateTime();
        for ($i=0; $i < 7; $i++) { 
          $date = $today->format('Y-m-d');
          $dates[] = $date;
          $today->add(new \DateInterval('P1D'));
        }
        for ($i=0; $i < count($dates); $i++) { 
          $date = \DateTime::createFromFormat('Y-m-d', $dates[$i]);
          $date = $date->format('l, jS F Y');
          $games[$date] = Games::where('start_time','like',$dates[$i].'%')
                                      ->take(90)
                                      ->where('start_time','>',date('Y-m-d H:i:s'))
                                      ->where(function($query) use ($request) {
                                          $query->whereRaw('LOWER(options) LIKE ?', ['%' . strtolower($request->search) . '%']);
                                      })
                                      ->skip(0)
                                      ->orderByDesc('popularity')
                                      ->orderBy('start_time')
                                      ->when(count($path)>=2, function($query) use ($path){
                                        return $query->where('sport_id',Sports::getID($path[1]));
                                      })
                                      ->when(count($path)>=3, function($query) use ($path){
                                        return $query->where('category_id',Categories::getID($path[2],Sports::getID($path[1])));
                                      })
                                      ->get('id');
          $dates[$i] = $date;
        }
        if(count($path)>=2){
          $categories = Categories::where('sport_id',Sports::getID($path[1]))->get('name');
          $categories = array_map(function($category){
            return $category['name'];
          }, $categories->toArray());
        }
        // package the games maintaining the order ['date':<game>]
        for ($i=0; $i < 7; $i++) {
          $games[$dates[$i]] = array_map(function($game){
            return \GameType::fromID($game['id'])->package();
          }, $games[$dates[$i]]->toArray());
        }
        return response()->json([
          "categories" => $categories,
          "games" => $games,
        ]);
      } catch (\Exception $e) {
        return response()->json([ 
            'error' => $e->getMessage(),
            'request'=>$request->all()
        ], 500);
    }
  }

  public function create(Request $request)
  {
    try{
      $outcomes = [
          ['name'=>$request->home_team,'stake'=>0.0,'users'=>0,'odd'=>$request->home_odd],
          ['name'=>'draw','stake'=>0.0,'users'=>0,'odd'=>$request->neutral_odd],
          ['name'=>$request->away_team,'stake'=>0.0,'users'=>0,'odd'=>$request->away_odd]
      ];
      $game = new \GameType(
          Sports::getID($request->sport),
          Categories::getID($request->category,Sports::getID($request->sport)),
          $request->start_time,
          [$request->home_team,$request->away_team],
          $outcomes
      );
      $response = Games::create($game);
      return response()->json([
        'message' => 'succesfully created game',
        'game' => $response->gameID
      ]);
  } catch(\Exception $e){
      Log::info($e->getMessage());
  }
  }
  public static function pull()
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
          $periods = [-1,1,2,3,4,5,6,7];
          foreach($periods as $period){//fetch matches
              $sport_url = "https://api.betika.com/v1/uo/matches?period_id=" . $period . "&sport_id=" . $sport_id;
              $response = $client->request('GET', $sport_url);
              $body = $response->getBody();
              $matches = json_decode($body->getContents());
              foreach($matches->data as $match){// loop through indiviadual games
                  try{
                      $outcomes = [
                          ['name'=>GamesController::shorten($match->home_team),'stake'=>0.0,'users'=>0,'odd'=>$match->home_odd],
                          ['name'=>'Draw','stake'=>0.0,'users'=>0,'odd'=>$match->neutral_odd],
                          ['name'=>GamesController::shorten($match->away_team),'stake'=>0.0,'users'=>0,'odd'=>$match->away_odd]
                      ];
                      $game = new \GameType(
                          Sports::getID($sport->sport_name=='Soccer'?'Football':$sport->sport_name),
                          Categories::getID($match->competition_name,Sports::getID($sport->sport_name=='Soccer'?'Football':$sport->sport_name)),
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


  //helper functions
  public static function shorten($teamName) {
    $words = explode(' ', $teamName);
    if (count($words) >= 3) {
        return $words[0] . ' ' . strtoupper($words[1][0]) . '. ' . strtoupper($words[2][0]) . '.';
    }
    if (count($words) == 2) {
        if (strlen($words[0]) > 3) {
            return substr($words[0], 0, 3) . '. ' . $words[1];
        }
        return $words[0] . ' ' . substr($words[1], 0, 1) . '.';
    }
    if (strlen($teamName) > 10) {
        return substr($teamName, 0, 8) . '...';
    }

    return $teamName;
  }
}
