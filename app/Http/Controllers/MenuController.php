<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function menu(Request $request)
    {
        $Popular = array(
            array(
              "icon" => "icon-[tabler--ball-football]",
              "text" => "Premier League",
              "sport" => "Football",
            ),
            array(
              "icon" => "icon-[tabler--ball-football]",
              "text" => "Champions League",
              "sport" => "Football",
            ),
            array(
              "icon" => "icon-[ion--baseball-outline]",
              "text" => "MLB",
              "sport" => "Baseball",
            ),
            array(
              "icon" => "icon-[bx--cricket-ball]",
              "text" => "T20 World Cup",
              "sport" => "Cricket",
            ),
            array(
              "icon" => "icon-[fluent--sport-hockey-24-regular]",
              "text" => "NHL",
              "sport" => "Hockey",
            ),
          );
          
          $Sports = array(
            array(
              "icon" => "icon-[tabler--ball-football]",
              "sport" => "Football",
              "categories" => array("Premier League", "Champions League", "La Liga", "Serie A", "Bundesliga", "Ligue 1"),
            ),
            array(
              "icon" => "icon-[fluent--sport-basketball-24-regular]",
              "sport" => "Basketball",
              "categories" => array("NBA", "NBA Playoffs", "NBA Futures", "NBA Props"),
            ),
            array(
              "icon" => "icon-[solar--rugby-outline]",
              "sport" => "Rugby",
              "categories" => array("Six Nations", "Rugby World Cup", "Super Rugby", "The Rugby Championship"),
            ),
            array(
              "icon" => "icon-[ion--baseball-outline]",
              "sport" => "Baseball",
              "categories" => array("MLB", "NBA", "NHL", "NFL"),
            ),
            array(
              "icon" => "icon-[solar--tennis-outline]",
              "sport" => "Tennis",
              "categories" => array("ATP", "WTA", "Grand Slam", "Davis Cup"),
            ),
            array(
              "icon" => "icon-[bx--cricket-ball]",
              "sport" => "Cricket",
              "categories" => array("T20 World Cup", "IPL", "The Ashes", "Big Bash League"),
            ),
            array(
              "icon" => "icon-[fluent--sport-hockey-24-regular]",
              "sport" => "Ice Hockey",
              "categories" => array("NHL", "NHL Playoffs", "NHL Futures", "NHL Props"),
            ),
            array(
              "icon" => "icon-[maki--table-tennis]",
              "sport" => "Table Tennis",
              "categories" => array(),
            ),
            array(
              "icon" => "icon-[ph--volleyball]",
              "sport" => "Volleyball",
              "categories" => array(),
            ),
            array(
              "icon" => "icon-[hugeicons--boxing-glove-01]",
              "sport" => "Boxing",
              "categories" => array("Heavyweight", "Lightweight", "Middleweight", "Welterweight"),
            ),
            array(
              "icon" => "icon-[gravity-ui--target-dart]",
              "sport" => "Darts",
              "categories" => array(),
            ),
            array(
              "icon" => "icon-[icon-park-outline--waterpolo]",
              "sport" => "Water polo",
              "categories" => array(),
            ),
            array(
              "icon" => "icon-[solar--gamepad-linear]",
              "sport" => "eSoccer",
              "categories" => array(),
            ),
          );
          return response()->json([
            "Popular" => $Popular,
            "Sports" => $Sports,
          ]);
    }
}
