<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'sport_id','country','popularity'];
    public static function create($name, $sport){
        Log::info("\t- " . $name);
        $category = new Categories();
        $category->name = $name;
        $category->sport_id = $sport;
        $category->popularity = 0.0;
        $category->save();
    }
    /**
     * A static function that the id given the category name
     */
    public static function getID($name){
        $category = Categories::where('name', $name)->first();
        return $category->id;
    }
    public static function getName($id){
        $category = Categories::where('id', $id)->first();
        return $category->name;
    }
}
