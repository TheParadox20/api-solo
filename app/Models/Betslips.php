<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Betslips extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'bets'];
    public static function create(){}
}
