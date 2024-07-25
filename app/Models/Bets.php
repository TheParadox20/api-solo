<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bets extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'game_id', 'amount', 'status', 'result'];
    public static function create(){}
}
