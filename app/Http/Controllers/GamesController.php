<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $categories = ['Premier League','Champions League','La Liga','Serie A','Bundesliga','Ligue 1'];
        $games = array(
            "Saturday, 12th June 2024" => array(
              array(
                "category" => "Premier league",
                "options" => array("Brentford", "Chelsea"),
                "outcomes" => array(
                  array(
                    "name" => "Brentford",
                    "stake" => 2500,
                    "users" => 38,
                  ),
                  array(
                    "name" => "Draw",
                    "stake" => 1950,
                    "users" => 21,
                  ),
                  array(
                    "name" => "Chelsea",
                    "stake" => 10705,
                    "users" => 164,
                  ),
                ),
                "date" => "Sat 12th Jun",
                "time" => "15:00 pm",
                "stakes" => 14955,
              ),
            ),
            "Sunday, 13th June 2024" => array(
              array(
                "category" => "Premier league",
                "options" => array("Manchester United", "Arsenal"),
                "outcomes" => array(
                  array(
                    "name" => "Man. United",
                    "stake" => 90,
                    "users" => 2,
                  ),
                  array(
                    "name" => "Draw",
                    "stake" => 30,
                    "users" => 2,
                  ),
                  array(
                    "name" => "Arsenal",
                    "stake" => 60,
                    "users" => 3,
                  ),
                ),
                "date" => "Sat 13th Jun",
                "time" => "15:00 pm",
                "stakes" => 180,
              ),
            ),
            "Monday, 14th June 2024" => array(
              array(
                "category" => "Premier league",
                "options" => array("Liverpool", "Ipswich"),
                "outcomes" => array(
                  array(
                    "name" => "Liverpool",
                    "stake" => 50,
                    "users" => 3,
                  ),
                  array(
                    "name" => "Draw",
                    "stake" => 70,
                    "users" => 4,
                  ),
                  array(
                    "name" => "Ipswich",
                    "stake" => 20,
                    "users" => 1,
                  ),
                ),
                "date" => "Sat 14th Jun",
                "time" => "15:00 pm",
                "stakes" => 140,
              ),
            ),
          );
          return response()->json([
            "categories" => $categories,
            "games" => $games,
          ]);
    }
}
