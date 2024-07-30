<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Sports;

class MenuController extends Controller
{
    public function menu(Request $request)
    {
      $categories = Categories::orderByDesc('popularity')->take(5)->get(['sport_id','name']);
      $Popular = [];
      foreach ($categories as $category) {
        $Popular[] = array(
          "text" => $category['name'],
          "sport" => Sports::getName($category['sport_id']),
        );
      }

      $AllSports = Sports::orderByDesc('popularity')->get(['id','name']);
      $Sports = [];
      foreach ($AllSports as $sport) {
        $categories = Categories::where('sport_id',$sport['id'])
                                ->orderByDesc('popularity')
                                ->take(7)
                                ->get('name');
        $Sports[] = array(
          "sport" => $sport['name'],
          "categories" => array_map(function($category){
            return $category['name'];
          }, $categories->toArray()),
        );
      }
          
          return response()->json([
            "Popular" => $Popular,
            "Sports" => $Sports,
          ]);
    }
}
