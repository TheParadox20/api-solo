<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

use App\Models\Books;

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
        $content = $body->getContents();
        Log::info($content);
        return response($content);
    }
}
