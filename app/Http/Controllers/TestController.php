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
use App\Models\Live;

class TestController extends Controller
{
    public function books()
    {
        $books = Books::all();
        return response()->json($books);
    }
    /**
     * Summary of live
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function live(Request $request)
    {
        $client = new Client();
        $url = 'https://live.betika.com/v1/uo/matches';
        $params = ['page' => 1];
        $count = 0;
        $games = [];

        try {
            while (true) {
                $response = $client->get($url, ['query' => $params]);
                $data = json_decode($response->getBody()->getContents(), true);
                
                if (empty($data['data'])) {
                    break;
                }

                foreach ($data['data'] as $match) {
                    $home_team = $match['home_team'] ?? 'N/A';
                    $away_team = $match['away_team'] ?? 'N/A';
                    $current_score = $match['current_score'] ?? 'N/A';
                    $match_time = $match['match_time'] ?? 'N/A';
                    $start_time = $match['start_time'] ?? 'N/A';
                    $competition_name = $match['competition_name'] ?? 'N/A';
                    $nation = $match['category'] ?? 'N/A';
                    $sport_name = $match['sport_name'] ?? 'N/A';

                    if ($match_time === '0' || $match_time === 'N/A' || $match_time === null) {
                        $match_time = 'Time Unknown';
                    }

                    if ($current_score === '-:-') {
                        if ($match_time === 'Time Unknown') {
                            $match_status = 'Score Available but Time Unknown';
                        } else {
                            $match_status = 'Score Unavailable';
                        }
                    } else {
                        if ($match_time === 'Time Unknown') {
                            $match_status = 'Score Available but Time Unknown';
                        } else {
                            $match_status = 'Live';
                        }
                    }

                    $count++;
                    try{
                        Live::create([
                            'home_team' => $home_team,
                            'away_team' => $away_team,
                            'current_score' => $current_score,
                            'time' => $match_time,
                            'start_time' => $start_time,
                        ]);
                    } catch(\Exception $e){
                        Log::info($e->getMessage());
                    }
                    $games[] = [
                        'sport_name' => $sport_name,
                        'home_team' => $home_team,
                        'away_team' => $away_team,
                        'current_score' => $current_score,
                        'match_time' => $match_time,
                        'start_time' => $start_time,
                        'competition_name' => $competition_name,
                        'nation' => $nation,
                        'match_status' => $match_status
                    ];
                }

                $params['page'] += 1;
            }

            Log::info("Matches fetched: {$count}");
            return response()->json($games);

        } catch (\Exception $e) {
            Log::error("Error fetching data: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching data']);
        }
    }
}
