<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BetsController;
use App\Http\Controllers\BotsController;
use App\Http\Controllers\Web3Controller;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
// test routes
Route::get('/test/books', [TestController::class, 'books']);
Route::get('/test/live', [TestController::class, 'live']);
// user related routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index']);
    // bet related routes
    Route::post('/bet/place', [BetsController::class, 'place']);
    Route::post('/bet/edit', [BetsController::class, 'edit']);
    Route::post('/bet/delete', [BetsController::class, 'delete']);
    Route::get('/betslip', [BetsController::class, 'betslip']);
    Route::get('/bets', [BetsController::class, 'bets']);
});
Route::post('/signin', [UserController::class, 'signin']);
Route::post('/signup', [UserController::class, 'signup']);
// bots related routes
Route::get('/bots', [BotsController::class, 'index']);
Route::get('/bots/bets', [BotsController::class, 'index']);
Route::post('/bots/create', [BotsController::class, 'create']);
Route::post('/bots/bet/place', [BetsController::class, 'place']);
// menu related routes
Route::get('/menu', [MenuController::class, 'menu']);
// games related routes
Route::get('/games/all', [GamesController::class, 'index']);
Route::get('/home', [GamesController::class, 'home']);
Route::get('/games', [GamesController::class, 'games']);
Route::get('/games/pull', [GamesController::class, 'pull']);
Route::post('/games/create', [GamesController::class, 'create']);
// web3 related routes
Route::get('/web3/check', [Web3Controller::class, 'index']);
