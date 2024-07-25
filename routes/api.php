<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\GamesController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
// test routes
Route::get('/test/books', [TestController::class, 'books']);
Route::get('/test/pull', [TestController::class, 'betika']);
// user related routes
// menu related routes
Route::get('/menu', [MenuController::class, 'menu']);
// games related routes
Route::get('/home', [GamesController::class, 'home']);
Route::get('/games', [GamesController::class, 'games']);
// bet related routes
