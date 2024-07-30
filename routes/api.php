<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BetsController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
// test routes
Route::get('/test/books', [TestController::class, 'books']);
// user related routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index']);
    // bet related routes
    Route::post('/bet/place', [BetsController::class, 'place']);
});
Route::post('/signin', [UserController::class, 'signin']);
Route::post('/signup', [UserController::class, 'signup']);
// menu related routes
Route::get('/menu', [MenuController::class, 'menu']);
// games related routes
Route::get('/home', [GamesController::class, 'home']);
Route::get('/games', [GamesController::class, 'games']);
Route::get('/games/pull', [GamesController::class, 'pull']);
