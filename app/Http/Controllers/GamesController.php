<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\CustomTypes\GameType;
use App\Models\Sports;
use App\Models\Categories;
use App\Models\Games;

class GamesController extends Controller
{
    public function home(Request $request)
    {
        $popular = array(
            "Football" => array(
              "icon" => "icon-[tabler--ball-football]",
              "data" => array(
                array(
                  "category" => "Premier league",
                  "options" => array("Manchester United", "Arsenal"),
                  "outcomes" => array(
                    array(
                      "name" => "Man. United",
                      "stake" => 2500,
                      "users" => 38,
                    ),
                    array(
                      "name" => "Draw",
                      "stake" => 1950,
                      "users" => 21,
                    ),
                    array(
                      "name" => "Arsenal",
                      "stake" => 3705,
                      "users" => 64,
                    ),
                  ),
                  "date" => "Sat 12th Jun",
                  "time" => "15:00 pm",
                  "stakes" => 7955,
                ),
              ),
            ),
            "Basketball" => array(
              "icon" => "icon-[fluent--sport-basketball-24-regular]",
              "data" => array(
                array(
                  "icon" => "basketball",
                  "category" => "NBA",
                  "options" => array("Lakers", "Clippers"),
                  "outcomes" => array(
                    array(
                      "name" => "Lakers",
                      "stake" => 2500,
                      "users" => 38,
                    ),
                    array(
                      "name" => "Draw",
                      "stake" => 1950,
                      "users" => 21,
                    ),
                    array(
                      "name" => "Clippers",
                      "stake" => 3705,
                      "users" => 64,
                    ),
                  ),
                  "date" => "Sat 12th Jun",
                  "time" => "15:00 pm",
                  "stakes" => 7955,
                ),
              ),
            ),
        );
        return response()->json($popular);
    }
    public function games(Request $request)
    {
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
                                      ->when(count($path)>=2, function($query) use ($path){
                                        return $query->where('sport_id',Sports::getID($path[1]));
                                      })
                                      ->when(count($path)>3, function($query) use ($path){
                                        return $query->where('category_id',Categories::getID($path[2]));
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
    }
}
