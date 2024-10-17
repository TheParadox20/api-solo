<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class P2EController extends Controller
{
    //
    public function index(Request $request){
        $market1 = [
            'thumbnail'=> 'https://via.placeholder.com/150',
            'title'=>'Presidential Election Winner',
            'stakes'=>12740,
            'options'=>[
              ['name'=>'Joe Biden', 'percentage'=>12],
              ['name'=>'Donald Trump', 'percentage'=>15],
              ['name'=>'Kanye West', 'percentage'=>20]
            ],
            'chats'=>324
        ];
        $market2 = [
            'thumbnail'=> 'https://via.placeholder.com/150',
            'title'=>'Presidential Election Winner',
            'stakes'=>69740,
            'chats'=>715
        ];
        return response()->json([$market1,$market2]);
    }
}
