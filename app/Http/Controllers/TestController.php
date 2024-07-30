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
}
