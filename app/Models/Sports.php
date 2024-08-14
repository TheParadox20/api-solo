<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sports extends Model
{
    use HasFactory;
    protected $fillable = ['name','popularity'];
    public static function create($name){
        $sport = new Sports();
        $name = 'Soccer'==$name?'Football':$name;
        $sport->name = $name;
        $sport->popularity = 0.0;
        $exists = Sports::where('name', $name)->first();
        if($exists){
            return;
        }
        $sport->save();
        Log::info("\n\n" . $name . " :: sport added\n");
    }
    public static function getID($name){
        $sport = Sports::where('name', $name)->first();
        throw_if(!$sport, \Exception::class, "Sport not found");
        return $sport->id;
    }
    public static function getName($id){
        $sport = Sports::where('id', $id)->first();
        return $sport->name;
    }
}
