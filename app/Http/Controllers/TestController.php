<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Books;

class TestController extends Controller
{
    public function books()
    {
        $books = Books::all();
        return response()->json($books);
    }
}
