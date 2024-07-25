<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    use HasFactory;
    protected $fillable = ['sport_id', 'category_id','country_id','amount','stakers','start_time','end_time','match','popularity'];
    public static function create(){}
}
